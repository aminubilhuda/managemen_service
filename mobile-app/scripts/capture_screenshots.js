const puppeteer = require('puppeteer-core');
const path = require('path');
const fs = require('fs');

const CHROME_PATH = '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome';
const SCREENSHOT_DIR = path.join(__dirname, '../screenshots');

if (!fs.existsSync(SCREENSHOT_DIR)) {
  fs.mkdirSync(SCREENSHOT_DIR, { recursive: true });
}

async function sleep(ms) {
  return new Promise((resolve) => setTimeout(resolve, ms));
}

async function run() {
  console.log('Launching browser...');
  const browser = await puppeteer.launch({
    executablePath: CHROME_PATH,
    headless: 'new',
    args: [
      '--no-sandbox',
      '--disable-setuid-sandbox',
      '--disable-web-security',
      '--disable-features=IsolateOrigins,site-per-process'
    ],
    defaultViewport: {
      width: 412,
      height: 915,
      deviceScaleFactor: 2,
      isMobile: true,
      hasTouch: true,
    }
  });

  const page = await browser.newPage();
  
  // Listen to console for debugging
  page.on('console', msg => console.log('PAGE LOG:', msg.text()));

  try {
    console.log('Navigating to http://localhost:8081 ...');
    await page.goto('http://localhost:8081/', { waitUntil: 'networkidle0', timeout: 30000 });
    await sleep(2500);

    // 1. Screenshot Login Screen
    console.log('1. Capturing 01_LoginScreen.png ...');
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '01_LoginScreen.png'), fullPage: false });

    // 2. Click "Konfigurasi Server IP"
    console.log('2. Opening Server Config Screen ...');
    const configBtn = await page.evaluate(() => {
      const elements = Array.from(document.querySelectorAll('div, span, button, [role="button"]'));
      const target = elements.find(el => el.textContent && el.textContent.includes('Konfigurasi Server IP'));
      if (target) {
        target.click();
        return true;
      }
      return false;
    });

    if (configBtn) {
      await sleep(1500);
      console.log('Capturing 02_ServerConfigScreen.png ...');
      await page.screenshot({ path: path.join(SCREENSHOT_DIR, '02_ServerConfigScreen.png'), fullPage: false });

      // Click Uji Koneksi Server
      console.log('Testing server connection ...');
      await page.evaluate(() => {
        const elements = Array.from(document.querySelectorAll('div, span, button, [role="button"]'));
        const target = elements.find(el => el.textContent && el.textContent.includes('Uji Koneksi Server'));
        if (target) target.click();
      });
      await sleep(2000);
      await page.screenshot({ path: path.join(SCREENSHOT_DIR, '02b_ServerConfigSuccess.png'), fullPage: false });

      // Go back
      await page.evaluate(() => {
        // Look for back button or header back
        const btns = Array.from(document.querySelectorAll('[role="button"], div, span'));
        // usually Ionicons arrow-back or first button in header
        if (btns.length > 0) btns[0].click();
      });
      await sleep(1500);
    }

    // 3. Perform Login
    console.log('3. Logging in ...');
    // Ensure we are back on Login Screen
    await page.evaluate(() => {
      const elements = Array.from(document.querySelectorAll('div, span, button, [role="button"]'));
      const masukBtn = elements.find(el => el.textContent && el.textContent.trim() === 'Masuk' || el.textContent.includes('Masuk Akun'));
      // Find button that has "Masuk"
      const buttons = Array.from(document.querySelectorAll('div[role="button"], [role="button"]'));
      const submit = buttons.find(b => b.textContent && b.textContent.includes('Masuk'));
      if (submit) submit.click();
    });

    // Wait for dashboard
    await sleep(4000);

    // 4. Capture Dashboard
    console.log('4. Capturing 03_DashboardScreen.png ...');
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '03_DashboardScreen.png'), fullPage: false });

    // Helper to click bottom tab by label
    async function clickTab(label) {
      return page.evaluate((txt) => {
        const tabs = Array.from(document.querySelectorAll('[role="tab"], [role="button"], div, span'));
        const target = tabs.find(el => el.textContent && el.textContent.trim() === txt);
        if (target) {
          target.click();
          return true;
        }
        return false;
      }, label);
    }

    // 5. Tiket Tab
    console.log('5. Clicking Tab Tiket ...');
    await clickTab('Tiket');
    await sleep(2500);
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '04_TiketListScreen.png'), fullPage: false });

    // Open first ticket if available
    console.log('Opening ticket detail ...');
    const openedTicket = await page.evaluate(() => {
      // Find cards with SRV- or ticket card
      const cards = Array.from(document.querySelectorAll('div[role="button"], [role="button"]'));
      const ticketCard = cards.find(c => c.textContent && (c.textContent.includes('SRV-') || c.textContent.includes('Keluhan')));
      if (ticketCard) {
        ticketCard.click();
        return true;
      }
      return false;
    });

    if (openedTicket) {
      await sleep(2000);
      console.log('6. Capturing 05_TiketDetailScreen.png ...');
      await page.screenshot({ path: path.join(SCREENSHOT_DIR, '05_TiketDetailScreen.png'), fullPage: false });

      // Click Ubah Status
      console.log('Opening Status Update Modal ...');
      await page.evaluate(() => {
        const btns = Array.from(document.querySelectorAll('[role="button"], div, span'));
        const ubahBtn = btns.find(b => b.textContent && b.textContent.includes('Ubah Status'));
        if (ubahBtn) ubahBtn.click();
      });
      await sleep(1500);
      await page.screenshot({ path: path.join(SCREENSHOT_DIR, '06_StatusUpdateModal.png'), fullPage: false });

      // Close modal
      await page.evaluate(() => {
        const btns = Array.from(document.querySelectorAll('[role="button"], div, span'));
        const batal = btns.find(b => b.textContent && b.textContent.includes('Batal'));
        if (batal) batal.click();
      });
      await sleep(1000);

      // Back to Tiket List
      await clickTab('Tiket');
      await sleep(1500);
    }

    // 7. Intake Screen
    console.log('7. Opening Tiket Intake Screen ...');
    const intakeBtn = await page.evaluate(() => {
      const btns = Array.from(document.querySelectorAll('[role="button"], div, span'));
      const target = btns.find(b => b.textContent && (b.textContent.includes('Tiket Baru') || b.textContent.includes('+ Tiket')));
      if (target) {
        target.click();
        return true;
      }
      return false;
    });

    if (intakeBtn) {
      await sleep(2000);
      console.log('Capturing 07_TiketIntakeScreen.png ...');
      await page.screenshot({ path: path.join(SCREENSHOT_DIR, '07_TiketIntakeScreen.png'), fullPage: false });
      // Go back
      await clickTab('Tiket');
      await sleep(1500);
    }

    // 8. Kasir Tab
    console.log('8. Clicking Tab Kasir ...');
    await clickTab('Kasir');
    await sleep(2500);
    console.log('Capturing 08_InvoiceListScreen.png ...');
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '08_InvoiceListScreen.png'), fullPage: false });

    // Open first invoice if available
    const openedInvoice = await page.evaluate(() => {
      const cards = Array.from(document.querySelectorAll('div[role="button"], [role="button"]'));
      const invoiceCard = cards.find(c => c.textContent && (c.textContent.includes('INV-') || c.textContent.includes('Rp')));
      if (invoiceCard) {
        invoiceCard.click();
        return true;
      }
      return false;
    });

    if (openedInvoice) {
      await sleep(2000);
      console.log('9. Capturing 09_InvoiceDetailScreen.png ...');
      await page.screenshot({ path: path.join(SCREENSHOT_DIR, '09_InvoiceDetailScreen.png'), fullPage: false });

      // Click Catat Pembayaran
      console.log('Opening Pembayaran Modal ...');
      await page.evaluate(() => {
        const btns = Array.from(document.querySelectorAll('[role="button"], div, span'));
        const bayarBtn = btns.find(b => b.textContent && b.textContent.includes('Catat Pembayaran'));
        if (bayarBtn) bayarBtn.click();
      });
      await sleep(1500);
      await page.screenshot({ path: path.join(SCREENSHOT_DIR, '10_PembayaranModal.png'), fullPage: false });

      // Close modal
      await page.evaluate(() => {
        const btns = Array.from(document.querySelectorAll('[role="button"], div, span'));
        const batal = btns.find(b => b.textContent && b.textContent.includes('Batal'));
        if (batal) batal.click();
      });
      await sleep(1000);

      await clickTab('Kasir');
      await sleep(1500);
    }

    // 10. Pengeluaran
    console.log('10. Opening Pengeluaran Screen ...');
    const pengeluaranBtn = await page.evaluate(() => {
      const btns = Array.from(document.querySelectorAll('[role="button"], div, span'));
      const target = btns.find(b => b.textContent && b.textContent.includes('Pengeluaran'));
      if (target) {
        target.click();
        return true;
      }
      return false;
    });

    if (pengeluaranBtn) {
      await sleep(2000);
      console.log('Capturing 11_PengeluaranScreen.png ...');
      await page.screenshot({ path: path.join(SCREENSHOT_DIR, '11_PengeluaranScreen.png'), fullPage: false });

      // Catat Pengeluaran Modal
      await page.evaluate(() => {
        const btns = Array.from(document.querySelectorAll('[role="button"], div, span'));
        const catat = btns.find(b => b.textContent && b.textContent.includes('Catat Pengeluaran'));
        if (catat) catat.click();
      });
      await sleep(1500);
      await page.screenshot({ path: path.join(SCREENSHOT_DIR, '12_PengeluaranModal.png'), fullPage: false });

      // Close modal
      await page.evaluate(() => {
        const btns = Array.from(document.querySelectorAll('[role="button"], div, span'));
        const batal = btns.find(b => b.textContent && b.textContent.includes('Batal'));
        if (batal) batal.click();
      });
      await sleep(1000);
    }

    // 11. Master Tab
    console.log('11. Clicking Tab Master ...');
    await clickTab('Master');
    await sleep(2500);
    console.log('Capturing 13_MasterScreen.png ...');
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '13_MasterScreen.png'), fullPage: false });

    // Click Tambah Pelanggan
    const tambahPelanggan = await page.evaluate(() => {
      const btns = Array.from(document.querySelectorAll('[role="button"], div, span'));
      const target = btns.find(b => b.textContent && b.textContent.includes('Tambah Pelanggan'));
      if (target) {
        target.click();
        return true;
      }
      return false;
    });

    if (tambahPelanggan) {
      await sleep(1500);
      console.log('Capturing 14_PelangganFormModal.png ...');
      await page.screenshot({ path: path.join(SCREENSHOT_DIR, '14_PelangganFormModal.png'), fullPage: false });

      // Close modal
      await page.evaluate(() => {
        const btns = Array.from(document.querySelectorAll('[role="button"], div, span'));
        const batal = btns.find(b => b.textContent && b.textContent.includes('Batal'));
        if (batal) batal.click();
      });
      await sleep(1000);
    }

    // 12. Akun Tab
    console.log('12. Clicking Tab Akun ...');
    await clickTab('Akun');
    await sleep(2500);
    console.log('Capturing 15_AkunScreen.png ...');
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '15_AkunScreen.png'), fullPage: false });

    // Click Edit Profil
    const editProfil = await page.evaluate(() => {
      const btns = Array.from(document.querySelectorAll('[role="button"], div, span'));
      const target = btns.find(b => b.textContent && b.textContent.includes('Edit Profil'));
      if (target) {
        target.click();
        return true;
      }
      return false;
    });

    if (editProfil) {
      await sleep(1500);
      console.log('Capturing 16_EditProfilModal.png ...');
      await page.screenshot({ path: path.join(SCREENSHOT_DIR, '16_EditProfilModal.png'), fullPage: false });

      // Close modal
      await page.evaluate(() => {
        const btns = Array.from(document.querySelectorAll('[role="button"], div, span'));
        const batal = btns.find(b => b.textContent && b.textContent.includes('Batal'));
        if (batal) batal.click();
      });
      await sleep(1000);
    }

    console.log('All screenshots captured successfully!');
  } catch (err) {
    console.error('Error during screenshot capture:', err);
  } finally {
    await browser.close();
  }
}

run();
