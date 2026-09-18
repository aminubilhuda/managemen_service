const puppeteer = require('puppeteer-core');
const path = require('path');
const fs = require('fs');

const CHROME_PATH = '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome';
const SCREENSHOT_DIR = path.join(__dirname, '../screenshots');

function sleep(ms) {
  return new Promise(resolve => setTimeout(resolve, ms));
}

async function run() {
  const browser = await puppeteer.launch({
    executablePath: CHROME_PATH,
    headless: 'new',
    args: ['--no-sandbox', '--disable-setuid-sandbox', '--disable-web-security'],
    defaultViewport: {
      width: 412,
      height: 915,
      deviceScaleFactor: 2,
      isMobile: true,
      hasTouch: true,
    }
  });

  const page = await browser.newPage();
  
  async function clickEl(text) {
    const handle = await page.evaluateHandle((t) => {
      const all = Array.from(document.querySelectorAll('*'));
      for (const el of all) {
        if (el.textContent && el.textContent.trim() === t) {
          return el.closest('[role="button"]') || el;
        }
      }
      return null;
    }, text);

    const el = handle.asElement();
    if (el) {
      const box = await el.boundingBox();
      if (box) {
        await page.mouse.click(box.x + box.width / 2, box.y + box.height / 2);
        await sleep(1500);
        return true;
      }
    }
    return false;
  }

  async function clickBack() {
    await page.evaluate(() => {
      const buttons = Array.from(document.querySelectorAll('[role="button"]'));
      const topBtn = buttons.find(b => {
        const r = b.getBoundingClientRect();
        return r.top < 80 && r.left < 80 && r.width > 0;
      });
      if (topBtn) topBtn.click();
    });
    await sleep(1500);
  }

  try {
    await page.goto('http://localhost:8081/', { waitUntil: 'networkidle0' });
    await sleep(2500);

    // 01. Login
    console.log('01. Login');
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '01_LoginScreen.png') });

    // 02. Server Config
    console.log('02. Server Config');
    await page.evaluate(() => {
      const all = Array.from(document.querySelectorAll('*'));
      const t = all.find(e => e.textContent && e.textContent.includes('Atur Alamat Server / IP'));
      if (t) (t.closest('[role="button"]') || t).click();
    });
    await sleep(1500);
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '02_ServerConfigScreen.png') });

    // Test connection
    console.log('02b. Uji Koneksi');
    await page.evaluate(() => {
      const all = Array.from(document.querySelectorAll('*'));
      const t = all.find(e => e.textContent && e.textContent.includes('Uji Koneksi Server'));
      if (t) (t.closest('[role="button"]') || t).click();
    });
    await sleep(2000);
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '02b_ServerConfigSuccess.png') });

    await clickBack();

    // 03. Login
    console.log('03. Dashboard');
    await page.evaluate(() => {
      const all = Array.from(document.querySelectorAll('*'));
      const t = all.find(e => e.textContent && e.textContent.trim() === 'Masuk Sekarang');
      if (t) (t.closest('[role="button"]') || t).click();
    });
    await sleep(4000);
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '03_DashboardScreen.png') });

    // 04. Servis Tab
    console.log('04. Servis Tab');
    await clickEl('Servis');
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '04_TiketListScreen.png') });

    // 05. Tiket Detail
    console.log('05. Tiket Detail');
    await page.evaluate(() => {
      const all = Array.from(document.querySelectorAll('*'));
      const card = all.find(e => e.textContent && e.textContent.includes('SRV-2026-000001'));
      if (card) (card.closest('[role="button"]') || card).click();
    });
    await sleep(2000);
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '05_TiketDetailScreen.png') });

    // 06. Status Update Modal
    console.log('06. Ubah Status Modal');
    await page.evaluate(() => {
      const all = Array.from(document.querySelectorAll('*'));
      const btn = all.find(e => e.textContent && e.textContent.includes('Ubah Status'));
      if (btn) (btn.closest('[role="button"]') || btn).click();
    });
    await sleep(1500);
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '06_StatusUpdateModal.png') });

    // Dismiss modal
    await page.evaluate(() => {
      const all = Array.from(document.querySelectorAll('*'));
      const b = all.find(e => e.textContent && e.textContent.trim() === 'Batal');
      if (b) (b.closest('[role="button"]') || b).click();
    });
    await sleep(1000);

    // Back to Servis List
    await clickBack();

    // 07. Servis Baru (Intake)
    console.log('07. Tiket Intake Screen');
    await page.evaluate(() => {
      // Find FAB or Intake button
      const buttons = Array.from(document.querySelectorAll('[role="button"]'));
      const fab = buttons.find(b => {
        const r = b.getBoundingClientRect();
        return r.bottom > 750 && r.right > 300 && r.width > 40;
      });
      if (fab) fab.click();
    });
    await sleep(2000);
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '07_TiketIntakeScreen.png') });

    await clickBack();

    // 08. Kasir Tab
    console.log('08. Kasir Tab');
    await clickEl('Kasir');
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '08_InvoiceListScreen.png') });

    // 09. Invoice Detail
    console.log('09. Invoice Detail');
    await page.evaluate(() => {
      const all = Array.from(document.querySelectorAll('*'));
      const card = all.find(e => e.textContent && e.textContent.includes('INV-2026-000001'));
      if (card) (card.closest('[role="button"]') || card).click();
    });
    await sleep(2000);
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '09_InvoiceDetailScreen.png') });

    // 10. Pembayaran Modal
    console.log('10. Pembayaran Modal');
    await page.evaluate(() => {
      const all = Array.from(document.querySelectorAll('*'));
      const btn = all.find(e => e.textContent && e.textContent.includes('Catat Pembayaran'));
      if (btn) (btn.closest('[role="button"]') || btn).click();
    });
    await sleep(1500);
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '10_PembayaranModal.png') });

    // Dismiss modal
    await page.evaluate(() => {
      const all = Array.from(document.querySelectorAll('*'));
      const b = all.find(e => e.textContent && e.textContent.trim() === 'Batal');
      if (b) (b.closest('[role="button"]') || b).click();
    });
    await sleep(1000);

    await clickBack();

    // 11. Buat Invoice Baru Screen (FAB)
    console.log('11. Buat Invoice Screen');
    await page.evaluate(() => {
      const buttons = Array.from(document.querySelectorAll('[role="button"]'));
      const fab = buttons.find(b => {
        const r = b.getBoundingClientRect();
        return r.bottom > 750 && r.right > 300 && r.width > 40;
      });
      if (fab) fab.click();
    });
    await sleep(2000);
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '11_InvoiceCreateScreen.png') });

    await clickBack();

    // 12. Kas Keluar (Pengeluaran)
    console.log('12. Pengeluaran Screen');
    await page.evaluate(() => {
      const all = Array.from(document.querySelectorAll('*'));
      const btn = all.find(e => e.textContent && e.textContent.includes('Kas Keluar'));
      if (btn) (btn.closest('[role="button"]') || btn).click();
    });
    await sleep(2000);
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '12_PengeluaranScreen.png') });

    // 13. Catat Pengeluaran Modal
    console.log('13. Pengeluaran Modal');
    await page.evaluate(() => {
      const all = Array.from(document.querySelectorAll('*'));
      const btn = all.find(e => e.textContent && e.textContent.includes('Catat Pengeluaran'));
      if (btn) (btn.closest('[role="button"]') || btn).click();
    });
    await sleep(1500);
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '13_PengeluaranModal.png') });

    // Dismiss modal
    await page.evaluate(() => {
      const all = Array.from(document.querySelectorAll('*'));
      const b = all.find(e => e.textContent && e.textContent.trim() === 'Batal');
      if (b) (b.closest('[role="button"]') || b).click();
    });
    await sleep(1000);

    await clickBack();

    // 14. Master Tab
    console.log('14. Master Tab');
    await clickEl('Master');
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '14_MasterScreen.png') });

    // 15. Pelanggan Detail
    console.log('15. Pelanggan Detail');
    await page.evaluate(() => {
      const all = Array.from(document.querySelectorAll('*'));
      const card = all.find(e => e.textContent && e.textContent.includes('Andi Setiawan'));
      if (card) (card.closest('[role="button"]') || card).click();
    });
    await sleep(2000);
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '15_PelangganDetailScreen.png') });

    await clickBack();

    // 16. Master Sparepart & Jasa
    console.log('16. Master Sparepart & Jasa');
    await clickEl('Sparepart & Jasa');
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '16_MasterProdukScreen.png') });

    // 17. Akun Tab
    console.log('17. Akun Tab');
    await clickEl('Akun');
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '17_AkunScreen.png') });

    // 18. Ubah Profil Modal
    console.log('18. Ubah Profil Modal');
    await page.evaluate(() => {
      const all = Array.from(document.querySelectorAll('*'));
      const btn = all.find(e => e.textContent && e.textContent.includes('Ubah Profil Saya'));
      if (btn) (btn.closest('[role="button"]') || btn).click();
    });
    await sleep(1500);
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '18_EditProfilModal.png') });

    // Dismiss
    await page.evaluate(() => {
      const all = Array.from(document.querySelectorAll('*'));
      const b = all.find(e => e.textContent && e.textContent.trim() === 'Batal');
      if (b) (b.closest('[role="button"]') || b).click();
    });
    await sleep(1000);

    // 19. Ganti Password Modal
    console.log('19. Ganti Password Modal');
    await page.evaluate(() => {
      const all = Array.from(document.querySelectorAll('*'));
      const btn = all.find(e => e.textContent && e.textContent.includes('Ganti Kata Sandi'));
      if (btn) (btn.closest('[role="button"]') || btn).click();
    });
    await sleep(1500);
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '19_GantiPasswordModal.png') });

    // Dismiss
    await page.evaluate(() => {
      const all = Array.from(document.querySelectorAll('*'));
      const b = all.find(e => e.textContent && e.textContent.trim() === 'Batal');
      if (b) (b.closest('[role="button"]') || b).click();
    });
    await sleep(1000);

    console.log('ALL SCREENSHOTS COMPLETED SUCCESSFULLY!');
  } catch (e) {
    console.error('Error:', e);
  } finally {
    await browser.close();
  }
}

run();
