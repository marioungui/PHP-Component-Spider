<?php

/*
The filter is constructed with a xPath argument
if there is a match to the xPath argument
it will give the given element in a array.
*/
switch (strtolower($arg["c"])) {
	case 'mvp':
		$component = "MVP Block";
		$filter = "//*[@class='mvp-block']";
		break;
	case 'search':
		$component = "Smart Question Search Engine Block";
		$filter = "//*[@class='sqe-block']";
		break;
	case 'related-articles':
		$component = "Related Articles Block";
		$filter = "//h2[text()='Artigos relacionados' or text()='Artigos Relacionados' or text()='Articulos Relacionados' or text()='Articulos relacionados' ]";
		break;
	case 'related-products':
		$component = "Related Products Block";
		$filter = "//h2[text()='Produtos Relacionados' or text()='Produtos Relacionados' or text()='Productos relacionados' or text()='Productos Relacionados']";
		break;
	case 'brand-carousel':
		$component = "Brands Block";
		$filter = "//*[starts-with(@id, 'brands_block')]/@id";
		break;
	case 'stages-block':
		$component = "Stages Block";
		$filter = "//*[starts-with(@id, 'stages_block')]/@id";
		break;
	case 'word':
		$component = "String search '".$word."'";
		$filter = "//*[contains(text(),'".$word."')]";
		break;	
	default:
		$component = "MVP Block";
		$filter = "//*[@class='mvp-block']";
		break;
}

