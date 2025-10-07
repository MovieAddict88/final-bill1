from playwright.sync_api import sync_playwright

def run(playwright):
    browser = playwright.chromium.launch(headless=True)
    context = browser.new_context()
    page = context.new_page()

    try:
        # Navigate to the login page
        page.goto("http://localhost:8000/customer_login.php")

        # Fill in the login code and submit
        page.fill("input[name='login_code']", "customer_test_code")
        page.click("button[type='submit']")

        # Wait for navigation to the dashboard and for the table to be visible
        page.wait_for_url("**/customer_dashboard.php")
        page.wait_for_selector("h4:has-text('Payment History')")

        # Take a screenshot of the updated table
        screenshot_path = "jules-scratch/verification/customer_dashboard.png"
        page.screenshot(path=screenshot_path)

        print(f"Screenshot saved to {screenshot_path}")

    except Exception as e:
        print(f"An error occurred: {e}")
        # Capture a screenshot for debugging if something goes wrong
        page.screenshot(path="jules-scratch/verification/error.png")

    finally:
        browser.close()

with sync_playwright() as playwright:
    run(playwright)