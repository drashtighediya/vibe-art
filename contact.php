<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact Us - Online Art Gallery</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      line-height: 1.6;
      margin: 30px;
      background: linear-gradient(to right, #667eea, #764ba2);
    }
    h1 {
      color: white;
      text-align: center;
    }
    .contact-container {
      max-width: 600px;
      margin: 20px auto;
      background: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .contact-info {
      margin-bottom: 20px;
    }
    .contact-info p {
      margin: 5px 0;
    }
    form input, form textarea {
      width: 98%;
      padding: 10px;
      margin: 8px 0;
      border: 1px solid #ccc;
      border-radius: 5px;
    }
    form button {
      background: #4a00e0;
      color: #fff;
      border: none;
      padding: 10px 20px;
      border-radius: 5px;
      cursor: pointer;
    }
    form button:hover {
      background-color: #8e2de2;
    }
  </style>
</head>
<body>

  <h1>Contact Us</h1>
  <div class="contact-container">
    
    <div class="contact-info">
      <p><strong>Email:</strong> ghediyadrashti2@email.com</p>
      <p><strong>Phone:</strong> +91 98765 43210</p>
      <p><strong>Address:</strong> Jamnagar, Gujarat, India</p>
    </div>

    <form action="send_message.php" method="POST">
      <input type="text" name="name" placeholder="Your Name" required>
      <input type="email" name="email" placeholder="Your Email" required>
      <textarea name="message" rows="5" placeholder="Your Message" required style="resize:none; heghit:120px;" ></textarea>
      <button type="submit">Send Message</button>
    </form>

  </div>

</body>
</html>
