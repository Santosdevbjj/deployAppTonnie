# Imagem leve de Nginx
FROM nginx:alpine

# Copia app estático
COPY index.html /usr/share/nginx/html/index.html
COPY css.css     /usr/share/nginx/html/css.css
COPY js.js       /usr/share/nginx/html/js.js

# Nginx custom (com proxy /api → backend)
COPY nginx.conf /etc/nginx/nginx.conf

# Healthcheck simples no container
HEALTHCHECK --interval=30s --timeout=3s CMD wget -qO- http://localhost:8080/ || exit 1
