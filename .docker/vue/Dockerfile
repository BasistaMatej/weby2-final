FROM node:latest as builder

WORKDIR /app

COPY FE /app

RUN npm install
RUN npm run dev

FROM nginx:latest

COPY --from=builder /app/dist /usr/share/nginx/html

EXPOSE 7070
