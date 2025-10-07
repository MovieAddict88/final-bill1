from playwright.sync_api import sync_playwright, expect

def run(playwright):
    browser = playwright.chromium.launch(headless=True)
    context = browser.new_context()
    page = context.new_page()

    try:
        # Navigate to the login page
        page.goto("http://localhost:8000/login.php")

        # Fill in the login form
        page.fill("input[name='username']", "test_employer")
        page.fill("input[name='password']", "password")

        # Click the login button
        page.click("button:has-text('Log in')")

        # Wait for navigation to the dashboard
        page.wait_for_url("http://localhost:8000/index.php")

        # Verify the dashboard heading is visible
        expect(page.locator("h3:has-text('Employer Dashboard')")).to_be_visible()

        # Wait for the progress bar container to have at least one item, indicating the AJAX call is complete
        progress_bar_item = page.locator(".progress-bar-item").first
        expect(progress_bar_item).to_be_visible(timeout=15000) # Increased timeout

        # Take a screenshot of the full page
        page.screenshot(path="jules-scratch/verification/employer_dashboard_final.png", full_page=True)
        print("Screenshot captured successfully.")

    except Exception as e:
        print(f"An error occurred: {e}")
        page.screenshot(path="jules-scratch/verification/error.png", full_page=True)
        with open("jules-scratch/verification/error.html", "w") as f:
            f.write(page.content())
        print("Error page content saved to jules-scratch/verification/error.html")

    finally:
        # Clean up
        context.close()
        browser.close()

with sync_playwright() as playwright:
    run(playwright)