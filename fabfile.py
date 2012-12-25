from fabric.api import sudo, cd, env
env.hosts = ['skiliks.com']

def deploy():
    with cd('/var/www/backend'):
        sudo('git pull origin master', user='skiliks')
        sudo('phing -Dstage=real')

    with cd('/var/www/front'):
        sudo('git pull origin master', user='skiliks')
        sudo('phing -Dstage=real')

