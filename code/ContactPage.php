<?php
/******************
 * 
 * ContactPage
 * 
 ******************/

//Model
class ContactPage extends Page
{
	private static $db = array(
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
	private static $nameCaption = 'Name*';
	private static $emailCaption = 'Email*';
	private static $mesageFieldCaption = 'Message*';
	
	public static function setNameCaption( $nameCaption ){
		self::$nameCaption = $nameCaption;
	}
	
	public static function setEmailCaption( $emailCaption ){
		self::$emailCaption = $emailCaption;
	}
	
	public static function setMessageFieldCaption( $messageFieldCaption ){
		self::$mesageFieldCaption = $messageFieldCaption;
	}
	
	//Define our ContactForm function as allowed
	private static $allowed_actions = array(
		'ContactForm'
	);
	
	//The function which generates our ContactForm
	function ContactForm() 
	{
      	// Create fields
	    $fields = new FieldList(
		    new TextField('Name', self::$nameCaption),
			new EmailField('Email', self::$emailCaption),
			new TextareaField('Comments',self::$mesageFieldCaption)
		);
	 	
	    // Create action
	    $actions = new FieldList(
	    	new FormAction('SendContactForm', 'Send')
				    );
	    
	    // Create action
 	    $validator = new RequiredFields('Name', 'Email', 'Comments');
	    
 	    $form = new Form($this, 'ContactForm', $fields, $actions, $validator);
	    
 	    if($form->hasExtension('FormSpamProtectionExtension')) {
 	    	$form->enableSpamProtection(  );
 	    }
 	    
 	    return $form;
	}
 	
	//The function that handles our ContactForm submission
	function SendContactForm($data, $ContactForm) 
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