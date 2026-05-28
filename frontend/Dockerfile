# --- Build Stage ---
FROM node:22-alpine AS builder
WORKDIR /app

# Declare Build Arguments (Vite requires these at compile-time)
ARG VITE_APP_NAME
ARG VITE_BASE_API_URL

# Expose them as Env variables during the build process
ENV VITE_APP_NAME=$VITE_APP_NAME
ENV VITE_BASE_API_URL=$VITE_BASE_API_URL

COPY package*.json ./
RUN npm ci

COPY . .
RUN npm run build

# --- Production Serve Stage ---
FROM nginx:stable-alpine
# Copy static built assets to Nginx default folder
COPY --from=builder /app/dist /usr/share/nginx/html
EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]