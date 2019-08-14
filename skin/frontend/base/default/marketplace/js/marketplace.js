  function removeAllClass()
  {
	  $$('.seller-review').each(function (e) {
				e.removeClassName('active-review'); 
			});
  }
  function hideShowReview(element)
    {   
		if(element.next().hasClassName('active-review'))
		{	
			removeAllClass();
		}
		else
		{
			removeAllClass();
			element.next().addClassName('active-review');
		}   
    }