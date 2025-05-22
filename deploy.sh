#!/bin/bash

# Nettoyer les anciens déploiements
echo "Cleaning up old deployments..."
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