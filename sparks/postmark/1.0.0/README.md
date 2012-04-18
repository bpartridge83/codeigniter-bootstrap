#Postmark API Wrapper for CodeIgniter Sparks

A Sparks library for CodeIgniter 2.0+ which extends the Core CI_Email class.

##Installation

	php tools/spark install postmark

##Using the Library

###Configuration

There is only one setting you need to update in the config file (application/config/postmark.php) and that is your Postmark API key. You can find your API key from the Server Details -> Credentials page in your Postmark Account (http://postmarkapp.com)

	$config['postmark_api_key'] = "YOUR_API_KEY_HERE";

###Loading the Library

In order to use the library, you will need to load it with Sparks.

	$this->load->spark('postmark');

###Sending an Email

The great thing about extending the Core CI_Email class is the ability to not have to change the way you use the class! The only difference is that you will be calling functions as $this->postmark->function_name() instead of $this->email->function_name().

##Contact

If you'd like to request an update, report bugs or contact me for any other reason, email me at [jrtashjian@gmail.com](mailto:jrtashjian@gmail.com)
