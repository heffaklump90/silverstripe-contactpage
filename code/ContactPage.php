<?php
/******************
 * 
 * ContactPage
 * 
 * Tutorial on www.ssbits.com/creating-a-simple-contact-form/
 * 
 * Author: Aram Balakjian of aabweb.co.uk
 * 
 ******************/

//Model
class ContactPage extends Page
{
	static $db = array(
		'Mailto' => 'Varchar(100)', //Email address to send submissions to
		'SubmitText' => 'HTMLText' //Text presented after submitting message
	);
	
	//CMS fields
	function getCMSFields() 
	{
		$fields = parent::getCMSFields();
	
		$fields->addFieldToTab("Root.OnSubmission", new TextField('Mailto', 'Email submissions to'));	
		$fields->addFieldToTab("Root.OnSubmission", new HTMLEditorField('SubmitText', 'Text on Submission'));	
	
		return $fields;	
	}

}

// Controller
class ContactPage_Controller extends Page_Controller
{
	//Define our form function as allowed
	static $allowed_actions = array(
		'ContactForm'
	);
	
	//The function which generates our form
	function Form() 
	{
      	// Create fields
	    $fields = new FieldList(
		    new TextField('Name', 'Name*'),
			new EmailField('Email', 'Email*'),
			new TextareaField('Comments','Comments*')
		);
	 	
	    // Create action
	    $actions = new FieldList(
	    	new FormAction('SendContactForm', 'Send')
				    );
	    
	    // Create action
	    $validator = new RequiredFields('Name', 'Email', 'Comments');
	    
	    $form = new Form($this, 'ContactForm', $fields, $actions, $validator);
	    
	    SpamProtectorManager::update_form($form, 'Message');

	    return $form;
	}
 	
	//The function that handles our form submission
	function SendContactForm($data, $form) 
	{
	 	//Set data
		$From = $data['Email'];
		$To = $this->Mailto;
		$Subject = "Website Contact message";  	  
		$email = new Email($From, $To, $Subject);
		//set template
		$email->setTemplate('ContactEmail');
		//populate template
		$email->populateTemplate($data);
		//send mail
		$email->send();
	  	//return to submitted message
		$this::redirect(Director::baseURL(). $this->URLSegment . "/?success=1");

	}

	//The function to test whether to display the Submit Text or not
	public function Success()
	{
		return isset($_REQUEST['success']) && $_REQUEST['success'] == "1";
	}
}