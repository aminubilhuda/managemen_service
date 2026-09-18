const puppeteer = require('puppeteer-core');

const CHROME_PATH = '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome';

async function testLogin() {
  const browser = await puppeteer.launch({
    executablePath: CHROME_PATH,
    headless: 'new',
    args: ['--no-sandbox', '--disable-setuid-sandbox']
  });

  const page = await browser.newPage();
  page.on('console', msg => console.log('BROWSER:', msg.text()));

  await page.goto('http://localhost:8081/', { waitUntil: 'networkidle0' });
  await new Promise(r => setTimeout(r, 2000));

  // Find all elements with text "Masuk Sekarang"
  const clicked = await page.evaluate(() => {
    const all = Array.from(document.querySelectorAll('*'));
    const btn = all.find(el => el.children.length === 0 && el.textContent && el.textContent.trim() === 'Masuk Sekarang');
    if (btn) {
      // Find closest clickable ancestor or click itself
      const clickable = btn.closest('[role="button"]') || btn.parentElement || btn;
      clickable.click();
      return 'Clicked: ' + clickable.tagName + ' / ' + btn.textContent;
    }
    return 'Not found';
  });

  console.log(clicked);

  await new Promise(r => setTimeout(r, 4000));

  // Check current text on page
  const text = await page.evaluate(() => document.body.innerText);
  console.log('Body text after click:\n', text.substring(0, 300));

  await browser.close();
}

testLogin();
