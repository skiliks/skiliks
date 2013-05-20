from fabric.api import sudo, cd, env
env.hosts = ['new.skiliks.com']

def deploy():
    with cd('/srv/www/skiliks'):
        sudo('git pull', user='skiliks')
        sudo('phing -Dstage=real', user='skiliks')
        sudo('touch protected/assets', user='skiliks')
        sudo('./yiic migrate', user='skiliks')

def reimport():
    with cd('/srv/www/skiliks'):
        sudo('./yiic import --method=All --scenario=lite', user='skiliks')
        sudo('./yiic import --method=All --scenario=full', user='skiliks')

