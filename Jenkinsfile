pipeline {
    agent any

    environment {
        APP_NAME = "php-app"
        CONTAINER_NAME = "php-production"
        PORT = "8083"
    }

    stages {

        stage('Source Checkout') {
            steps {
                checkout scm
                sh 'pwd'
                sh 'ls -la'
            }
        }

        stage('Composer Install') {
            steps {
                sh '''
                if [ -f composer.json ]; then
                    composer install --no-interaction --prefer-dist --no-progress
                else
                    echo "composer.json not found. Skipping Composer Install."
                fi
                '''
            }
        }

        stage('PHP Syntax Validation') {
            steps {
                sh '''
                php -l index.php
                '''
            }
        }

        stage('Docker Image Build') {
            steps {
                sh '''
                docker build -t ${APP_NAME}:${BUILD_NUMBER} .
                '''
            }
        }

        stage('Trivy Scan') {
            steps {
                sh '''
                trivy image --severity HIGH,CRITICAL ${APP_NAME}:${BUILD_NUMBER} || true
                '''
            }
        }

        stage('Deployment') {
            steps {
                sh '''
                docker stop ${CONTAINER_NAME}-old || true
                docker rm ${CONTAINER_NAME}-old || true

                docker rename ${CONTAINER_NAME} ${CONTAINER_NAME}-old || true

                docker run -d \
                  --name ${CONTAINER_NAME} \
                  -p ${PORT}:80 \
                  ${APP_NAME}:${BUILD_NUMBER}
                '''
            }
        }

        stage('Health Verification') {
            steps {
                sh '''
                sleep 10

                curl -f http://localhost:${PORT}
                '''
            }
        }
    }

    post {

        success {
            echo 'Deployment Successful'

            sh '''
            docker rm -f ${CONTAINER_NAME}-old || true
            '''
        }

        failure {
            echo 'Deployment Failed - Starting Rollback'

            sh '''
            docker stop ${CONTAINER_NAME} || true
            docker rm ${CONTAINER_NAME} || true

            docker rename ${CONTAINER_NAME}-old ${CONTAINER_NAME} || true

            docker start ${CONTAINER_NAME} || true
            '''
        }
    }
}
