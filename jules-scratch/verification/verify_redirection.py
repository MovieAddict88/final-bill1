from playwright.sync_api import sync_playwright, expect

def run(playwright):
    browser = playwright.chromium.launch(headless=True)
    context = browser.new_context()
    page = context.new_page()

    def handle_dialog(dialog):
        print(f"Dialog message: {dialog.message}")
        dialog.accept()

    page.on("dialog", handle_dialog)

    try:
        # Admin login
        page.goto("http://localhost:8000/login.php")
        page.fill('input[name="username"]', "admin")
        page.fill('input[name="password"]', "password")
        page.click('button[type="submit"]')
        expect(page).to_have_url("http://localhost:8000/index.php")

        # Create employer
        page.goto("http://localhost:8000/user.php")
        page.click('button[id="add"]')

        # Fill out the modal form
        modal = page.locator('div#add_data_Modal')
        modal.wait_for(state='visible')

        modal.locator('input[name="username"]').fill("testemployer")
        modal.locator('input[name="password"]').fill("password")
        modal.locator('input[name="repassword"]').fill("password")
        modal.locator('input[name="email"]').fill("employer@test.com")
        modal.locator('input[name="fullname"]').fill("Test Employer")
        modal.locator('input[name="address"]').fill("123 Test St")
        modal.locator('input[name="contact"]').fill("1234567890")
        modal.locator('select[name="role"]').select_option("employer")
        modal.locator('button[type="submit"]').click()
        modal.wait_for(state='hidden')

        # Create customer
        page.goto("http://localhost:8000/customers.php")
        page.click('button#add:has-text("Add New Customer")')

        customer_modal = page.locator('div#add_data_Modal')
        customer_modal.wait_for(state='visible')

        customer_modal.locator('input[name="full_name"]').fill("Test Customer")
        customer_modal.locator('input[name="nid"]').fill("123456789")
        customer_modal.locator('input[name="address"]').fill("456 Customer Ave")
        customer_modal.locator('input[name="email"]').fill("customer@test.com")
        customer_modal.locator('input[name="conn_location"]').fill("Test Location")
        customer_modal.locator('select[name="package"]').select_option("1")
        customer_modal.locator('select[name="employer"]').select_option(label="Test Employer")
        customer_modal.locator('input[name="ip_address"]').fill("127.0.0.1")
        customer_modal.locator('input[name="conn_type"]').fill("DHCP")
        customer_modal.locator('input[name="contact"]').fill("0987654321")
        customer_modal.locator('button[type="submit"]').click()
        customer_modal.wait_for(state='hidden')

        # Generate bill
        page.goto("http://localhost:8000/bill_generation.php")
        page.click('button[type="submit"]')

        # Logout
        page.goto("http://localhost:8000/logout.php")

        # Employer login
        page.goto("http://localhost:8000/login.php")
        page.fill('input[name="username"]', "testemployer")
        page.fill('input[name="password"]', "password")
        page.click('button[type="submit"]')
        expect(page).to_have_url("http://localhost:8000/index.php")

        # Get customer ID from the pay button link
        pay_button = page.locator('a:has-text("Pay")')
        href = pay_button.get_attribute("href")
        customer_id = href.split("customer=")[1]

        # Go to manual payment page
        page.goto(f"http://localhost:8000/manual_payment.php?customer={customer_id}")

        # Submit payment
        page.check('input[type="checkbox"]')
        page.fill('input[name="amount"]', "800")
        page.fill('input[name="reference_number"]', "12345")
        page.click('button[type="submit"]')

        # Verify redirection
        expect(page).to_have_url("http://localhost:8000/index.php")
        expect(page.locator("h3")).to_have_text("Employer Dashboard")

        # Take screenshot
        page.screenshot(path="jules-scratch/verification/redirection_verification.png")

    finally:
        browser.close()

with sync_playwright() as p:
    run(p)