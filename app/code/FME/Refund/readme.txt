How to install Refund Request Extension on Localhost:

    1. Install Magento2 SMTP Extension (For sending and receiving email)
        1. Configure the required fields
            1. Set Host name to “ smtp.gmail.com “
            2. Set Port number 465
            3. Set Protocol to SSL
            4. Set Authentication to Login
            5. Set the username and password ( Use your own email address and password )
    2. Now it's time to send a testing email
        1. Set Send From to “ General Contact “
        2. Send To “ type recieving email address“
    3.  If you successfully received email then its time to test the refund extension
    4. Install the Refund Request Extension
        1. Sign in to your account as a customer
        2. Place an order 
        3. Go to “My orders” Section
        4. You should see a link “Refund” next to your order details 
        5. Click on refund , a popup form will appear, provide the required fields
        6. Click on submit 
        7. You should receive an email on the provided email address
    5. Now Login as Admin
    6. Go to the FME EXTENSION menu 
    7. Click on Refund Request
    8. You should see a detailed grid for all the refund requests 
    9. In the Action Column Click on accept or reject email to close the order and refund the order to the customer
    10. Click on FME/ Configuration menu to set the required configurations for Refund extension    
    11. Do Remember to set the admin emial if you want to recieve email notifications
