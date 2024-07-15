# Use the official WordPress image as a parent image
FROM wordpress:latest

# Switch to root to install dependencies
USER root

# Install required dependencies
RUN apt-get update && apt-get install -y \
    python3 \
    python3-pip \
    git

# Install Aider
RUN pip3 install aider-chat

# Switch back to the default WordPress user
USER www-data

# Set the working directory
WORKDIR /var/www/html

# The CMD instruction should be inherited from the parent image
