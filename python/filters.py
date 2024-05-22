import sys

# Obtén los argumentos de la línea de comandos
args = sys.argv[1:]

# Asegúrate de que el número de argumentos sea par
if len(args) % 2 != 0:
    raise ValueError("Número impar de argumentos proporcionados. Asegúrate de proporcionar pares de argumentos clave-valor.")

# Convierte la lista de argumentos en un diccionario
arg = {args[i][1:]: args[i+1] for i in range(0, len(args), 2)}

# Define el filtro basado en el argumento proporcionado
component = ""
filter = ""

if arg["c"] in ["mvp", "1"]:
    component = "MVP Block"
    filter = "//*[@class='mvp-block']"
elif arg["c"] in ["search", "2"]:
    component = "Smart Question Search Engine Block"
    filter = "//*[@class='sqe-block']"
elif arg["c"] in ["related-articles", "3"]:
    component = "Related Articles Block"
    filter = "//h2[text()='Artigos relacionados' or text()='Artigos Relacionados' or text()='Articulos Relacionados' or text()='Articulos relacionados' ]"
elif arg["c"] in ["related-products", "4"]:
    component = "Related Products Block"
    filter = "//h2[text()='Produtos Relacionados' or text()='Produtos Relacionados' or text()='Productos relacionados' or text()='Productos Relacionados']"
elif arg["c"] in ["brand-carousel", "5"]:
    component = "Brands Block"
    filter = "//*[starts-with(@id, 'brands_block')]/@id"
elif arg["c"] in ["stages-block", "6"]:
    component = "Stages Block"
    filter = "//*[starts-with(@id, 'stages_block')]"
elif arg["c"] in ["word", "7"]:
    word = arg["w"]
    component = f"String search '{word}'"
    filter = f"//*[contains(text(),'{word}')]"
elif arg["c"] in ["action-bar", "8"]:
    component = "Action Bar"
    filter = "//div[contains(@class, 'action-bar__wrapper')]"
elif arg["c"] in ["links", "9"]:
    word = arg["w"]
    component = f"Links containing {word}"
    filter = f"//a[contains(@href, '{word}')]"
elif arg["c"] in ["cta", "10"]:
    component = "Stages Block using From Library"
    filter = "//div[contains(@class, 'paragraph--type--stages-block')]//div[contains(@class, 'grid-col-10')]"
else:
    raise ValueError("Invalid component specified")
