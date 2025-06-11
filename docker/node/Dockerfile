# базовый образ с Node.js
FROM node:22

RUN apt-get update && apt-get install -y \
    vim \
    zip \
    unzip \
    curl

# Change current user
USER node

EXPOSE 5174

CMD ["sh", "-c", "npm install && npm run dev -- --host"]
