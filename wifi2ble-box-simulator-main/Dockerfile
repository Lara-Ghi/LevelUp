FROM python:3.11-slim

WORKDIR /app

COPY . .

# install a tiny TCP forwarder
RUN apt-get update && apt-get install -y socat && rm -rf /var/lib/apt/lists/*

# expose the public-facing port (the one socat will listen on)
EXPOSE 8000

# 1) start simulator bound to localhost:8001
# 2) expose it on 0.0.0.0:8000 via socat
CMD ["bash","-lc","python simulator/main.py --port 8001 & exec socat TCP-LISTEN:8000,fork,reuseaddr TCP:127.0.0.1:8001"]
