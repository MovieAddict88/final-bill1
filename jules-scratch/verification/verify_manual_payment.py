from playwright.sync_api import sync_playwright

def run(playwright):
    browser = playwright.chromium.launch(headless=True)
    context = browser.new_context()
    page = context.new_page()

    try:
        # Navigate to the login page
        page.goto("http://localhost:8080/login.php")

        # Fill in the login form
        page.fill('input[name="user_name"]', "admin")
        page.fill('input[name="user_pwd"]', "admin")

        # Click the login button
        page.click('button[type="submit"]')

        # Wait for navigation to the dashboard (or bills page)
        page.wait_for_url("http://localhost:8080/index.php")

        # Navigate to the bills page
        page.goto("http://localhost:8080/bills.php")

        # Find the "Manual Pay" button and click it
        manual_pay_button = page.locator("text=Manual Pay").first
        manual_pay_button.click()

        # Wait for the new page to load
        with context.expect_page() as new_page_info:
            pass  # The click should have already opened the new page

        new_page = new_page_info.value
        new_page.wait_for_load_state()

        # Take a screenshot of the manual payment page
        new_page.screenshot(path="jules-scratch/verification/manual_payment_page.png")

        print("Screenshot saved to jules-scratch/verification/manual_payment_page.png")

    except Exception as e:
        print(f"An error occurred: {e}")
        page.screenshot(path="jules-scratch/verification/error.png")

    finally:
        browser.close()

with sync_playwright() as playwright:
    run(playwright)