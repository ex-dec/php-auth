# Use the official PHP image as the base image
FROM php:8.1-cli

# Set the working directory
WORKDIR /var/www/html

# Copy the current directory contents into the container
COPY . /var/www/html

# Expose port 5000 to the outside world
EXPOSE 5000

# Start the PHP built-in server on port 5000
CMD ["php", "-S", "0.0.0.0:5000", "-t", "/var/www/html"]
