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

                sh "ls -la"

            }

        }



        stage('Composer Install') {

            steps {

                sh '''

                if [ -f composer.json ]; then

                    docker run --rm \
                    -v /var/jenkins_home/workspace/php-app-pipeline:/app \
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
                -v /var/jenkins_home/workspace/php-app-pipeline:/app \
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

                trivy image ${APP_NAME}:${BUILD_NUMBER} || true

                '''

            }

        }




        stage('Deployment') {

            steps {

                sh '''

                docker stop ${CONTAINER_NAME} || true


                docker rm ${CONTAINER_NAME} || true



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

        }



        failure {


            echo "Deployment Failed"


            sh '''

            docker stop ${CONTAINER_NAME} || true

            docker rm ${CONTAINER_NAME} || true


            '''

        }


    }

}
