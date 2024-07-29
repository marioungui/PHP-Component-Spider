<?php

/*
The filter is constructed with a xPath argument
if there is a match to the xPath argument
it will give the given element in a array.
*/
switch (strtolower($arg["c"])) {
	case 'mvp':
	case 1:
		$component = "MVP Block";
		$filter = "//*[@class='mvp-block']";
		break;
	case 'search':
	case 2:
		$component = "Smart Question Search Engine Block";
		$filter = "//*[@class='sqe-block']";
		break;
	case 'related-articles':
	case 3:
		$component = "Related Articles Block";
		$filter = "//h2[text()='Artigos relacionados' or text()='Artigos Relacionados' or text()='Articulos Relacionados' or text()='Articulos relacionados' ]";
		break;
	case 'related-products':
	case 4:
		$component = "Related Products Block";
		$filter = "//h2[text()='Produtos Relacionados' or text()='Produtos Relacionados' or text()='Productos relacionados' or text()='Productos Relacionados']";
		break;
	case 'brand-carousel':
	case 5:
		$component = "Brands Block";
		$filter = "//*[starts-with(@id, 'brands_block')]/@id";
		break;
	case 'stages-block':
	case 6:
		$component = "Stages Block";
		$filter = "//*[starts-with(@id, 'stages_block')]";
		break;
	case 'word':
	case 7:
		$component = "String search '".$word."'";
		$filter = "//*[contains(text(),'".$word."')]";
		break;
	case 'action-bar':
	case 8:
		$component = "Action Bar";
		$filter = "//div[contains(@class, 'action-bar__wrapper')]";
		break;
	case 'links':
	case 9:
		$component = "Links containing ".$word."";
		$filter = "//a[contains(@href, '".$word."')]";
		break;
	case 10:
	case 'metatitle':
		$component = "SEO Metadata Title & Description";
		$filter = "SEO Meta";
		break;
	case 11:
	case 'new-tab':
		$component = "Links that open in a new tab";
		$filter = "//a[@target='_blank']";
		break;
	case 12:
	case 'ndg-broken-pages':
		$component = "NDG Pages containing Bootstrap";
		$filter = "//link[@href='https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css']";
		break;
	case 13:
	case 'h1-length':
		$component = "H1 Length";
		$filter = "H1 Length";
		break;
	case 14:
	case 'no-teaser':
		$component = "No intro text on NBBNM article";
		$filter = "//div[contains(@class, 'main-info-block__text') and string-length(normalize-space(.)) < 5]";
		break;
	}