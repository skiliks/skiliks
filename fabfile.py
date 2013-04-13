from fabric.api import sudo, cd, env
env.hosts = ['new.skiliks.com']

def deploy():
    with cd('/srv/www/skiliks'):
        sudo('git pull', user='skiliks')
        sudo('phing -Dstage=real', user='skiliks')
        sudo('service php5-fpm reload')

