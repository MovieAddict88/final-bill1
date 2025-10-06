from playwright.sync_api import sync_playwright, expect

def run(playwright):
    browser = playwright.chromium.launch(headless=True)
    context = browser.new_context()
    page = context.new_page()

    # Navigate to the login page
    # IMPORTANT: The base URL needs to be adapted to the running environment.
    # Since I cannot run the server myself, I have to assume a base URL.
    # I will assume it is running on localhost:8000
    base_url = "http://localhost:8000"

    try:
        page.goto(f"{base_url}/login.php")

        # Log in as admin
        page.get_by_placeholder("User Name").fill("admin")
        page.get_by_placeholder("Password").fill("12345678")
        page.get_by_role("button", name="Login").click()

        # Wait for navigation to the dashboard (index.php)
        expect(page).to_have_url(f"{base_url}/index.php")

        # Navigate to the employee monitoring page
        page.goto(f"{base_url}/employee_monitoring.php")

        # Wait for the page to load and the monitoring container to be visible
        expect(page.locator(".monitoring-container")).to_be_visible()

        # Take a screenshot
        page.screenshot(path="jules-scratch/verification/verification.png")

        print("Screenshot saved to jules-scratch/verification/verification.png")

    except Exception as e:
        print(f"An error occurred: {e}")
        # Take a screenshot on error to help debug
        page.screenshot(path="jules-scratch/verification/error.png")

    finally:
        browser.close()

with sync_playwright() as playwright:
    run(playwright)