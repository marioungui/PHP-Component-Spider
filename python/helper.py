import sys
import socket
import re

def color_log(message, type='i'):
    color_codes = {
        'e': '\033[31m',  # Red
        's': '\033[32m',  # Green
        'w': '\033[33m',  # Yellow
        'i': '\033[36m',  # Cyan
    }
    reset_code = '\033[0m'
    color_code = color_codes.get(type, '\033[36m')
    print(f"{color_code}{message}{reset_code}")

def check_domain(domain):
    try:
        socket.gethostbyname(domain)
        return True
    except socket.error:
        return False

def validate_domain(url, domain):
    url = re.sub(r'^https?://', '', url)
    url = re.sub(r'^www\.', '', url)
    url_host = url.split('/')[0]

    if url_host == domain:
        return True
    elif url_host.endswith('.' + domain):
        return True
    else:
        raise Exception("URL is not inside the specified domain")
