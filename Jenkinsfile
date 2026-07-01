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

                echo "Code checked out from GitHub"

            }

        }



        stage('Composer Install') {

            steps {

                sh '''

                if [ -f composer.json ]; then

                    docker run --rm \
                    -v $(pwd):/app \
                    -w /app \
                    composer:latest \
                    composer install

                else

                    echo "No composer.json found"

                fi

                '''

            }

        }



        stage('PHP Syntax Validation') {

            steps {

                sh '''

                docker run --rm \
                -v $(pwd):/app \
                -w /app \
                php:8.3-cli \
                php -l index.php

                '''

            }

        }



        stage('Docker Image Build') {

            steps {

                sh '''

                docker build \
                -t ${APP_NAME}:${BUILD_NUMBER} .

                '''

            }

        }




        stage('Trivy Security Scan') {

            steps {

                sh '''

                trivy image \
                ${APP_NAME}:${BUILD_NUMBER} || true

                '''

            }

        }





        stage('Deployment') {

            steps {

                sh '''

                docker stop ${CONTAINER_NAME} || true

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

                sleep 5


                curl -f http://localhost:${PORT}


                '''

            }

        }


    }



    post {


        success {

            echo "Deployment Successful"

            sh '''

            docker rm -f ${CONTAINER_NAME}-old || true

            '''

        }



        failure {


            echo "Deployment Failed - Rolling Back"


            sh '''

            docker stop ${CONTAINER_NAME} || true

            docker rm ${CONTAINER_NAME} || true


            docker rename ${CONTAINER_NAME}-old ${CONTAINER_NAME} || true


            docker start ${CONTAINER_NAME} || true


            '''

        }


    }

}
