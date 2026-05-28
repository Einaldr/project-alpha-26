# --- Build Stage ---
FROM node:22-alpine AS builder
WORKDIR /app

# 1. Cache dependencies (Best Practice)
COPY package*.json ./
RUN npm ci

# 2. Declare Build Arguments
ARG VITE_APP_NAME
ARG VITE_BASE_API_URL

# 3. Pass them to the build environment
ENV VITE_APP_NAME=$VITE_APP_NAME
ENV VITE_BASE_API_URL=$VITE_BASE_API_URL

COPY . .

# 4. Optional: Type-check before building to ensure TS safety
# RUN npm run type-check 

RUN npm run build

# --- Production Serve Stage ---
FROM nginx:stable-alpine

# 5. Add custom config to handle React Router / SPA routing
COPY nginx.conf /etc/nginx/conf.d/default.conf

# 6. Copy static built assets
COPY --from=builder /app/dist /usr/share/nginx/html

EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]