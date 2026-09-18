const puppeteer = require('puppeteer-core');
const path = require('path');
const fs = require('fs');

const CHROME_PATH = '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome';
const SCREENSHOT_DIR = path.join(__dirname, '../screenshots');

if (!fs.existsSync(SCREENSHOT_DIR)) {
  fs.mkdirSync(SCREENSHOT_DIR, { recursive: true });
}

function sleep(ms) {
  return new Promise(r => setTimeout(r, ms));
}

async function run() {
  console.log('Launching browser with touch/retina display...');
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

  // Helper to click bottom tab
  async function clickTab(name) {
    await page.evaluate((tabName) => {
      const all = Array.from(document.querySelectorAll('*'));
      const target = all.find(e => e.children.length === 0 && e.textContent && e.textContent.trim() === tabName);
      if (target) {
        (target.closest('[role="button"]') || target.closest('[role="tab"]') || target).click();
      }
    }, name);
    await sleep(2000);
  }

  // Helper for programmatic navigation
  async function nav(screenName, params = {}) {
    await page.evaluate((s, p) => {
      if (window.__navigation && window.__navigation.navigate) {
        window.__navigation.navigate(s, p);
      }
    }, screenName, params);
    await sleep(2000);
  }

  try {
    console.log('Opening http://localhost:8081/ ...');
    await page.goto('http://localhost:8081/', { waitUntil: 'networkidle0', timeout: 30000 });
    await sleep(2500);

    // 01. Login Screen
    console.log('01. Capturing 01_LoginScreen.png ...');
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '01_LoginScreen.png') });

    // 02. Server Config Screen
    console.log('02. Opening Server Config Screen ...');
    await page.evaluate(() => {
      const all = Array.from(document.querySelectorAll('*'));
      const t = all.find(e => e.textContent && e.textContent.includes('Atur Alamat Server / IP'));
      if (t) (t.closest('[role="button"]') || t).click();
    });
    await sleep(1500);
    console.log('Capturing 02_ServerConfigScreen.png ...');
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '02_ServerConfigScreen.png') });

    // 02b. Test connection
    console.log('Testing connection in Server Config ...');
    await page.evaluate(() => {
      const all = Array.from(document.querySelectorAll('*'));
      const t = all.find(e => e.textContent && e.textContent.includes('Uji Koneksi Server'));
      if (t) (t.closest('[role="button"]') || t).click();
    });
    await sleep(2500);
    console.log('Capturing 02b_ServerConfigSuccess.png ...');
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '02b_ServerConfigSuccess.png') });

    // Go back to login
    await page.evaluate(() => {
      const all = Array.from(document.querySelectorAll('*'));
      const t = all.find(e => e.textContent && e.textContent.includes('Simpan & Terapkan'));
      if (t) (t.closest('[role="button"]') || t).click();
    });
    await sleep(1500);

    // 03. Login to Dashboard
    console.log('03. Logging into Dashboard ...');
    await page.evaluate(() => {
      const all = Array.from(document.querySelectorAll('*'));
      const t = all.find(e => e.textContent && e.textContent.trim() === 'Masuk Sekarang');
      if (t) (t.closest('[role="button"]') || t).click();
    });
    await sleep(4000);
    console.log('Capturing 03_DashboardScreen.png ...');
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '03_DashboardScreen.png') });

    // 04. Tab Servis (Tiket List)
    console.log('04. Navigating to Tab Servis ...');
    await clickTab('Servis');
    console.log('Capturing 04_TiketListScreen.png ...');
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '04_TiketListScreen.png') });

    // 05. Tiket Intake Screen
    console.log('05. Navigating to TiketIntake ...');
    await nav('TiketIntake');
    console.log('Capturing 05_TiketIntakeScreen.png ...');
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '05_TiketIntakeScreen.png') });

    // 06. Tiket Detail Screen (ID 1)
    console.log('06. Navigating to TiketDetail (id: 1) ...');
    await nav('TiketDetail', { id: 1 });
    console.log('Capturing 06_TiketDetailScreen.png ...');
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '06_TiketDetailScreen.png') });

    // 07. Ubah Status Modal
    console.log('07. Opening Ubah Status Modal ...');
    await page.evaluate(() => {
      const all = Array.from(document.querySelectorAll('*'));
      const t = all.find(e => e.textContent && e.textContent.includes('Ubah Status'));
      if (t) (t.closest('[role="button"]') || t).click();
    });
    await sleep(1500);
    console.log('Capturing 07_StatusUpdateModal.png ...');
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '07_StatusUpdateModal.png') });
    // Dismiss
    await page.evaluate(() => {
      const all = Array.from(document.querySelectorAll('*'));
      const b = all.find(e => e.textContent && e.textContent.trim() === 'Batal');
      if (b) (b.closest('[role="button"]') || b).click();
    });
    await sleep(1000);

    // 08. Tab Kasir (Invoice List)
    console.log('08. Navigating to Tab Kasir ...');
    await clickTab('Kasir');
    console.log('Capturing 08_InvoiceListScreen.png ...');
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '08_InvoiceListScreen.png') });

    // 09. Invoice Create Screen
    console.log('09. Navigating to InvoiceCreate ...');
    await nav('InvoiceCreate');
    console.log('Capturing 09_InvoiceCreateScreen.png ...');
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '09_InvoiceCreateScreen.png') });

    // 10. Invoice Detail Screen (ID 1)
    console.log('10. Navigating to InvoiceDetail (id: 1) ...');
    await nav('InvoiceDetail', { id: 1 });
    console.log('Capturing 10_InvoiceDetailScreen.png ...');
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '10_InvoiceDetailScreen.png') });

    // 11. Pembayaran Modal
    console.log('11. Opening Pembayaran Modal ...');
    await page.evaluate(() => {
      const all = Array.from(document.querySelectorAll('*'));
      const t = all.find(e => e.textContent && e.textContent.includes('Catat Pembayaran'));
      if (t) (t.closest('[role="button"]') || t).click();
    });
    await sleep(1500);
    console.log('Capturing 11_PembayaranModal.png ...');
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '11_PembayaranModal.png') });
    // Dismiss
    await page.evaluate(() => {
      const all = Array.from(document.querySelectorAll('*'));
      const b = all.find(e => e.textContent && e.textContent.trim() === 'Batal');
      if (b) (b.closest('[role="button"]') || b).click();
    });
    await sleep(1000);

    // 12. Pengeluaran Screen
    console.log('12. Navigating to Pengeluaran ...');
    await nav('Pengeluaran');
    console.log('Capturing 12_PengeluaranScreen.png ...');
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '12_PengeluaranScreen.png') });

    // 13. Catat Pengeluaran Modal
    console.log('13. Opening Catat Pengeluaran Modal ...');
    await page.evaluate(() => {
      const all = Array.from(document.querySelectorAll('*'));
      const t = all.find(e => e.textContent && e.textContent.includes('Catat Pengeluaran'));
      if (t) (t.closest('[role="button"]') || t).click();
    });
    await sleep(1500);
    console.log('Capturing 13_PengeluaranModal.png ...');
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '13_PengeluaranModal.png') });
    // Dismiss
    await page.evaluate(() => {
      const all = Array.from(document.querySelectorAll('*'));
      const b = all.find(e => e.textContent && e.textContent.trim() === 'Batal');
      if (b) (b.closest('[role="button"]') || b).click();
    });
    await sleep(1000);

    // 14. Tab Master (Pelanggan)
    console.log('14. Navigating to Tab Master ...');
    await clickTab('Master');
    console.log('Capturing 14_MasterPelangganScreen.png ...');
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '14_MasterPelangganScreen.png') });

    // 15. Pelanggan Detail Screen (ID 1)
    console.log('15. Navigating to PelangganDetail (id: 1) ...');
    await nav('PelangganDetail', { id: 1 });
    console.log('Capturing 15_PelangganDetailScreen.png ...');
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '15_PelangganDetailScreen.png') });

    // 16. Tab Master (Sparepart & Jasa)
    console.log('16. Tab Master Sparepart & Jasa ...');
    await clickTab('Master');
    await page.evaluate(() => {
      const all = Array.from(document.querySelectorAll('*'));
      const t = all.find(e => e.children.length === 0 && e.textContent && e.textContent.trim() === 'Sparepart & Jasa');
      if (t) (t.closest('[role="button"]') || t).click();
    });
    await sleep(2000);
    console.log('Capturing 16_MasterProdukScreen.png ...');
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '16_MasterProdukScreen.png') });

    // 17. Tab Akun
    console.log('17. Navigating to Tab Akun ...');
    await clickTab('Akun');
    console.log('Capturing 17_AkunScreen.png ...');
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '17_AkunScreen.png') });

    // 18. Ubah Profil Modal
    console.log('18. Opening Ubah Profil Modal ...');
    await page.evaluate(() => {
      const all = Array.from(document.querySelectorAll('*'));
      const t = all.find(e => e.textContent && e.textContent.includes('Ubah Profil Saya'));
      if (t) (t.closest('[role="button"]') || t).click();
    });
    await sleep(1500);
    console.log('Capturing 18_EditProfilModal.png ...');
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '18_EditProfilModal.png') });
    // Dismiss
    await page.evaluate(() => {
      const all = Array.from(document.querySelectorAll('*'));
      const b = all.find(e => e.textContent && e.textContent.trim() === 'Batal');
      if (b) (b.closest('[role="button"]') || b).click();
    });
    await sleep(1000);

    // 19. Ganti Password Modal
    console.log('19. Opening Ganti Password Modal ...');
    await page.evaluate(() => {
      const all = Array.from(document.querySelectorAll('*'));
      const t = all.find(e => e.textContent && e.textContent.includes('Ganti Kata Sandi'));
      if (t) (t.closest('[role="button"]') || t).click();
    });
    await sleep(1500);
    console.log('Capturing 19_GantiPasswordModal.png ...');
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '19_GantiPasswordModal.png') });
    // Dismiss
    await page.evaluate(() => {
      const all = Array.from(document.querySelectorAll('*'));
      const b = all.find(e => e.textContent && e.textContent.trim() === 'Batal');
      if (b) (b.closest('[role="button"]') || b).click();
    });
    await sleep(1000);

    console.log('MASTER SCREENSHOT RUNNER FINISHED 100% SUCCESSFULLY!');
  } catch (err) {
    console.error('Master capture error:', err);
  } finally {
    await browser.close();
  }
}

run();
