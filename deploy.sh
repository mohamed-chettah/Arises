#!/bin/bash

# Nettoyer les conteneurs et déploiements existants
echo "Cleaning up existing containers and deployments..."
docker compose down --remove-orphans
docker rm -f $(docker ps -aq) 2>/dev/null || true
kubectl delete deployment --all -n arises
kubectl delete service --all -n arises

# Build des images
echo "Building Docker images..."
docker build -t arises-frontend:latest ./frontend
docker build -t arises-backend:latest ./backend

# Appliquer les configurations Kubernetes
echo "Applying Kubernetes configurations..."
kubectl apply -f k8s/ -n arises

# Vérifier le statut du déploiement
echo "Checking deployment status..."
kubectl get pods -n arises
kubectl get services -n arises 