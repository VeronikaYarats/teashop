	
	/* Изменнение стиля активного раздела */
	window.onload=function()
	{ 
		for (var lnk = document.links, j = 0; j < lnk.length; j++) 
			if (lnk [j].href == document.URL) {
				lnk [j].style.color = '#edee6f'; 
				lnk [j].style.background = 'rgba(50,50,50,0.2)';
			}
		}

	/* Проверяем что все поля добавления/редактирования продукта заполнены */
	function check_product_fields() 
    {
		var forma;
		forma = document.getElementById("add_edit");
        if (!forma.name.value)
        	alert("Не введено название");
        else
        	if (!forma.country.value)
        		alert("Не введена страна");
        	else 
        		if (!forma.weight.value)
        			alert("Не введен вес");
        		else 
        			if (!forma.price.value)
        				alert("Не введена цена");
        			else 
        				if((forma.image.value) && (forma.image_url.value))  
        					alert("Загрузите картинку только через одно поле");
        				else                                   
        					forma.submit();
    }
	
	/* Проверяем что все поля добавления/редактирования статьи заполнены */
	function check_article_fields() 
	{
		var forma;
		forma = document.getElementById("add_edit");
		if (!forma.key.value)
			alert("Не введен key");
		else
			if (!forma.page_title.value)
				alert("Не введен title");
			else 
				if (!forma.name.value)
					alert("Не введено название статьи");
				else 
					if (!forma.contents.value)
						alert("Не введено содержание статьи");
					else
 	    			    forma.submit();
	}