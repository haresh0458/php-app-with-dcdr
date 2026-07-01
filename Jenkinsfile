pipeline {

agent any

stages {

stage('Checkout') {
steps {
git branch: 'main',
url: 'https://github.com/haresh0458/php-app-with-dcdr.git'
}
}


stage('PHP Syntax Check') {
steps {
sh 'php -l index.php'
}
}


stage('Docker Build') {
steps {
sh 'docker build -t php-app:$BUILD_NUMBER .'
}
}


stage('Deploy') {
steps {

sh '''
docker stop php-new || true
docker rm php-new || true

docker run -d \
--name php-new \
-p 8083:80 \
php-app:$BUILD_NUMBER

'''

}
}


stage('Health Check') {
steps {

sh '''
curl -f localhost:8083

'''

}

}

}

}
