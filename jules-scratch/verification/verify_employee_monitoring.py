from playwright.sync_api import sync_playwright, expect

def run(playwright):
    browser = playwright.chromium.launch(headless=True)
    context = browser.new_context()
    page = context.new_page()

    # The user is not logged in, so I'll need to log in as an admin first.
    # I'll navigate to the login page and enter credentials.
    page.goto("http://localhost:8000/login.php")
    page.get_by_placeholder("username").fill("admin")
    page.get_by_placeholder("password").fill("password")
    page.get_by_role("button", name="Log in").click()

    # Now, navigate to the employee monitoring page
    page.goto("http://localhost:8000/employee_monitoring.php")

    # Wait for the cards to be visible
    expect(page.locator(".employer-card").first).to_be_visible()

    # Take a screenshot
    page.screenshot(path="jules-scratch/verification/verification.png")

    browser.close()

with sync_playwright() as playwright:
    run(playwright)