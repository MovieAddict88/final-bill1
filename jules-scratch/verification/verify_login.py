from playwright.sync_api import sync_playwright, expect

def run(playwright):
    browser = playwright.chromium.launch(headless=True)
    context = browser.new_context()
    page = context.new_page()

    try:
        # Admin login
        page.goto("http://localhost:8000/login.php")
        page.fill('input[name="username"]', "admin")
        page.fill('input[name="password"]', "password")
        page.click('button[type="submit"]')

        page.wait_for_load_state("domcontentloaded")

        # Check for the error message
        error_div = page.locator("div.panel-warning")
        if error_div.is_visible():
            print("Error message found:", error_div.inner_text())
        else:
            print("No error message found on page.")

        print("Current URL:", page.url)

        # Take a screenshot for debugging
        page.screenshot(path="jules-scratch/verification/login_attempt.png")

        # Expect to be redirected to index.php
        expect(page).to_have_url("http://localhost:8000/index.php")

    finally:
        browser.close()

with sync_playwright() as p:
    run(p)