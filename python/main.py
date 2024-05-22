import sys
import csv
import requests
from lxml import html
from urllib.parse import urlparse
from helper import color_log, check_domain, validate_domain
from filters import component, filter, arg

def create_csv(filename, header):
    with open(filename, 'w', newline='', encoding='utf-8') as file:
        writer = csv.writer(file)
        writer.writerow(header)
    return filename

def append_to_csv(filename, row):
    with open(filename, 'a', newline='', encoding='utf-8') as file:
        writer = csv.writer(file)
        writer.writerow(row)

def test_search_for_word(sitemap, filter, writer, countok, countfail):
    for url in sitemap:
        try:
            response = requests.get(url, timeout=30)
            response.raise_for_status()
        except requests.RequestException:
            continue
        
        page = html.fromstring(response.content)
        elements = page.xpath(filter)
        if elements:
            print(f"{url} FOUND {len(elements)} TIMES")
            append_to_csv(writer, [url, f"FOUND {len(elements)} TIMES"])
            countok += 1
        else:
            print(f"{url} NOT FOUND")
            append_to_csv(writer, [url, "NOT FOUND"])
            countfail += 1

def test_search_for_component(sitemap, filter, writer, countok, countfail, countdup):
    for url in sitemap:
        try:
            response = requests.get(url, timeout=30)
            response.raise_for_status()
        except requests.RequestException:
            append_to_csv(writer, [url, "HTTP Request Failed"])
            continue
        
        page = html.fromstring(response.content)
        elements = page.xpath(filter)
        count = len(elements)
        if count > 1:
            print(f"{url} DUPLICATED")
            append_to_csv(writer, [url, "DUPLICATED"])
            countdup += 1
        elif count == 1:
            print(f"{url} OK")
            append_to_csv(writer, [url, "OK"])
            countok += 1
        else:
            print(f"{url} NOT FOUND")
            append_to_csv(writer, [url, "NOT FOUND"])
            countfail += 1

# Configuración inicial
if 'd' not in arg or 'c' not in arg:
    color_log("There is missing some variables, make sure that you are launching this script with the required parameters", "e")
    sys.exit(1)

domain = arg['d']
if not check_domain(domain):
    color_log("The domain isn't valid", "e")
    sys.exit(1)

if arg['c'] in ['word', '7', 'links', '9'] and 'w' not in arg:
    color_log("You should specify with the -w{word} parameter which word are you searching for", "e")
    sys.exit(1)

print(f"Searching for {component} on the site {domain}\nUsing the xPath filter {filter}")

writer_file = create_csv(f"{component}-{urlparse(domain).netloc}.csv", ["URL", "Result"])

sitemap = [domain]  # Sustituir con la generación real del sitemap

countok = 0
countfail = 0
countdup = 0

if arg["c"] in ['word', '7', 'links', '9']:
    test_search_for_word(sitemap, filter, writer_file, countok, countfail)
else:
    test_search_for_component(sitemap, filter, writer_file, countok, countfail, countdup)

print(f"Total of pages with {component} found: {countok}")
print(f"Total of pages with {component} not found: {countfail}")
print(f"Total of pages with {component} duplicated: {countdup}")
