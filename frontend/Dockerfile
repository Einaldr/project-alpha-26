FROM node:22-alpine

WORKDIR /app

# Install dependencies first (leverages Docker cache)
COPY package*.json ./
RUN npm install

COPY . .

EXPOSE 5173

# Start the Vite dev server exposing it to the docker network
CMD ["npm", "run", "dev", "--", "--host"]