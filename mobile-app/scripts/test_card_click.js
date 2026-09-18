const puppeteer = require('puppeteer-core');

const CHROME_PATH = '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome';

async function testCardClick() {
  const browser = await puppeteer.launch({
    executablePath: CHROME_PATH,
    headless: 'new',
    args: ['--no-sandbox', '--disable-setuid-sandbox']
  });

  const page = await browser.newPage();
  await page.setViewport({ width: 412, height: 915 });

  await page.goto('http://localhost:8081/', { waitUntil: 'networkidle0' });
  await new Promise(r => setTimeout(r, 2000));

  // Login
  const loginBtn = await page.evaluateHandle(() => {
    const all = Array.from(document.querySelectorAll('*'));
    const t = all.find(e => e.textContent && e.textContent.trim() === 'Masuk Sekarang');
    return t ? (t.closest('[role="button"]') || t) : null;
  });
  await loginBtn.click();
  await new Promise(r => setTimeout(r, 3500));

  // Servis tab
  const servisTab = await page.evaluateHandle(() => {
    const all = Array.from(document.querySelectorAll('*'));
    const t = all.find(e => e.children.length === 0 && e.textContent && e.textContent.trim() === 'Servis');
    return t ? (t.closest('[role="button"]') || t.closest('[role="tab"]') || t) : null;
  });
  await servisTab.click();
  await new Promise(r => setTimeout(r, 2000));

  // Click card with el.click()
  const card = await page.evaluateHandle(() => {
    const cards = Array.from(document.querySelectorAll('div[tabindex="0"]'));
    return cards.find(c => c.textContent.includes('SRV-2026-000001'));
  });

  console.log('Card found:', card !== null);
  await card.click();
  await new Promise(r => setTimeout(r, 2500));

  const bodyText = await page.evaluate(() => document.body.innerText.substring(0, 300));
  console.log('Body after click:\n', bodyText);

  await browser.close();
}

testCardClick();
