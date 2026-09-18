const puppeteer = require('puppeteer-core');
const path = require('path');
const fs = require('fs');

const CHROME_PATH = '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome';
const SCREENSHOT_DIR = path.join(__dirname, '../screenshots');

function sleep(ms) {
  return new Promise(r => setTimeout(r, ms));
}

async function capture() {
  const browser = await puppeteer.launch({
    executablePath: CHROME_PATH,
    headless: 'new',
    args: ['--no-sandbox', '--disable-setuid-sandbox'],
    defaultViewport: {
      width: 412,
      height: 915,
      deviceScaleFactor: 2,
      isMobile: true,
      hasTouch: true,
    }
  });

  const page = await browser.newPage();

  async function clickText(t) {
    const el = await page.evaluateHandle((text) => {
      const all = Array.from(document.querySelectorAll('*'));
      const target = all.find(e => e.children.length === 0 && e.textContent && e.textContent.trim() === text);
      return target ? (target.closest('[role="button"]') || target.closest('[role="tab"]') || target) : null;
    }, t);
    if (el) {
      await el.click();
      await sleep(2000);
      return true;
    }
    return false;
  }

  async function clickHeaderBack() {
    await page.evaluate(() => {
      const buttons = Array.from(document.querySelectorAll('[role="button"]'));
      // Find top left back button
      const backBtn = buttons.find(b => {
        const r = b.getBoundingClientRect();
        return r.top < 70 && r.left < 70;
      });
      if (backBtn) backBtn.click();
    });
    await sleep(2000);
  }

  try {
    console.log('Navigating to web app...');
    await page.goto('http://localhost:8081/', { waitUntil: 'networkidle0' });
    await sleep(2500);

    // 01. Login
    console.log('Capturing 01_LoginScreen.png');
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '01_LoginScreen.png') });

    // 02. Server Config
    console.log('Opening Server Config');
    await page.evaluate(() => {
      const all = Array.from(document.querySelectorAll('*'));
      const t = all.find(e => e.textContent && e.textContent.includes('Atur Alamat Server / IP'));
      if (t) (t.closest('[role="button"]') || t).click();
    });
    await sleep(2000);
    console.log('Capturing 02_ServerConfigScreen.png');
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '02_ServerConfigScreen.png') });

    // Test Server
    console.log('Testing connection...');
    await page.evaluate(() => {
      const all = Array.from(document.querySelectorAll('*'));
      const t = all.find(e => e.textContent && e.textContent.includes('Uji Koneksi Server'));
      if (t) (t.closest('[role="button"]') || t).click();
    });
    await sleep(2500);
    console.log('Capturing 02b_ServerConfigSuccess.png');
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '02b_ServerConfigSuccess.png') });

    // Back to Login
    await clickHeaderBack();

    // 03. Login to Dashboard
    console.log('Logging in...');
    await page.evaluate(() => {
      const all = Array.from(document.querySelectorAll('*'));
      const t = all.find(e => e.textContent && e.textContent.trim() === 'Masuk Sekarang');
      if (t) (t.closest('[role="button"]') || t).click();
    });
    await sleep(4000);
    console.log('Capturing 03_DashboardScreen.png');
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '03_DashboardScreen.png') });

    // 04. Quick Action: Servis Baru (Intake)
    console.log('Opening Servis Baru (Intake)...');
    await clickText('Servis Baru');
    console.log('Capturing 04_TiketIntakeScreen.png');
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '04_TiketIntakeScreen.png') });
    await clickHeaderBack();

    // 05. Quick Action: Scan Tiket
    console.log('Opening Scan Tiket...');
    await clickText('Scan Tiket');
    console.log('Capturing 05_TiketScanScreen.png');
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '05_TiketScanScreen.png') });
    await clickHeaderBack();

    // 06. Quick Action: Buat Invoice
    console.log('Opening Buat Invoice...');
    await clickText('Buat Invoice');
    console.log('Capturing 06_InvoiceCreateScreen.png');
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '06_InvoiceCreateScreen.png') });
    await clickHeaderBack();

    // 07. Quick Action: Kas Keluar (Pengeluaran)
    console.log('Opening Kas Keluar...');
    await clickText('Kas Keluar');
    console.log('Capturing 07_PengeluaranScreen.png');
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '07_PengeluaranScreen.png') });

    // Open Catat Pengeluaran Modal
    console.log('Opening Catat Pengeluaran Modal...');
    await page.evaluate(() => {
      const all = Array.from(document.querySelectorAll('*'));
      const t = all.find(e => e.textContent && e.textContent.includes('Catat Pengeluaran'));
      if (t) (t.closest('[role="button"]') || t).click();
    });
    await sleep(1500);
    console.log('Capturing 07b_PengeluaranModal.png');
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '07b_PengeluaranModal.png') });
    // Dismiss
    await page.evaluate(() => {
      const all = Array.from(document.querySelectorAll('*'));
      const b = all.find(e => e.textContent && e.textContent.trim() === 'Batal');
      if (b) (b.closest('[role="button"]') || b).click();
    });
    await sleep(1000);
    await clickHeaderBack();

    // 08. Tab Servis (Tiket List)
    console.log('Opening Tab Servis...');
    await clickText('Servis');
    console.log('Capturing 08_TiketListScreen.png');
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '08_TiketListScreen.png') });

    // 09. Tab Kasir (Invoice List)
    console.log('Opening Tab Kasir...');
    await clickText('Kasir');
    console.log('Capturing 09_InvoiceListScreen.png');
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '09_InvoiceListScreen.png') });

    // 10. Tab Master (Pelanggan)
    console.log('Opening Tab Master...');
    await clickText('Master');
    console.log('Capturing 10_MasterPelangganScreen.png');
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '10_MasterPelangganScreen.png') });

    // 11. Tab Master (Sparepart & Jasa)
    console.log('Switching to Sparepart & Jasa...');
    await clickText('Sparepart & Jasa');
    console.log('Capturing 11_MasterProdukScreen.png');
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '11_MasterProdukScreen.png') });

    // 12. Tab Akun
    console.log('Opening Tab Akun...');
    await clickText('Akun');
    console.log('Capturing 12_AkunScreen.png');
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '12_AkunScreen.png') });

    // 13. Ubah Profil Modal
    console.log('Opening Ubah Profil Modal...');
    await page.evaluate(() => {
      const all = Array.from(document.querySelectorAll('*'));
      const t = all.find(e => e.textContent && e.textContent.includes('Ubah Profil Saya'));
      if (t) (t.closest('[role="button"]') || t).click();
    });
    await sleep(1500);
    console.log('Capturing 13_EditProfilModal.png');
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '13_EditProfilModal.png') });
    // Dismiss
    await page.evaluate(() => {
      const all = Array.from(document.querySelectorAll('*'));
      const b = all.find(e => e.textContent && e.textContent.trim() === 'Batal');
      if (b) (b.closest('[role="button"]') || b).click();
    });
    await sleep(1000);

    // 14. Ganti Password Modal
    console.log('Opening Ganti Password Modal...');
    await page.evaluate(() => {
      const all = Array.from(document.querySelectorAll('*'));
      const t = all.find(e => e.textContent && e.textContent.includes('Ganti Kata Sandi'));
      if (t) (t.closest('[role="button"]') || t).click();
    });
    await sleep(1500);
    console.log('Capturing 14_GantiPasswordModal.png');
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '14_GantiPasswordModal.png') });
    // Dismiss
    await page.evaluate(() => {
      const all = Array.from(document.querySelectorAll('*'));
      const b = all.find(e => e.textContent && e.textContent.trim() === 'Batal');
      if (b) (b.closest('[role="button"]') || b).click();
    });
    await sleep(1000);

    console.log('ALL SCREENSHOTS CAPTURED PERFECTLY!');
  } catch (err) {
    console.error('Error during capture:', err);
  } finally {
    await browser.close();
  }
}

capture();
