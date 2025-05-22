#!/bin/bash

# Build des images
echo "Building Docker images..."
docker build -t frontend:latest ./frontend
docker build -t backend:latest ./backend

# Appliquer les configurations Kubernetes
echo "Applying Kubernetes configurations..."
kubectl apply -f k8s/ -n arises

# Vérifier le statut du déploiement
echo "Checking deployment status..."
kubectl get pods -n arises
kubectl get services -n arises 