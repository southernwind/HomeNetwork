node {
  stage('CheckOut'){
    checkout([$class: 'GitSCM', branches: [[name: '*/master']], doGenerateSubmoduleConfigurations: false, extensions: [], submoduleCfg: [], userRemoteConfigs: [[url: 'https://github.com/southernwind/HomeNetwork']]])
  }

  stage('Configuration'){
    configFileProvider([configFile(fileId: 'c2d55a3f-ec4b-45c3-8b3f-ff15ed21ba3f', targetLocation: 'group_vars/home.yaml')]) {}
  }

  stage('Ansible raspi_dhcp'){
    ansiblePlaybook credentialsId: 'b27d104d-bfbf-4381-9945-539905a7392e', inventory: 'inventory_jenkins', limit: 'dhcp', playbook: 'playbook.yaml'
  }

  stage('Ansible home_server'){
    ansiblePlaybook credentialsId: 'a03bad98-9a5c-4855-b3a9-27e120a90447', inventory: 'inventory_jenkins', limit: 'home', playbook: 'playbook.yaml'
  }

  stage('Ansible aquarium_raspi'){
    ansiblePlaybook credentialsId: '84abccbc-f317-430f-be13-ea3d41b7e713', inventory: 'inventory_jenkins', limit: 'aquarium', playbook: 'playbook.yaml'
  }

  stage('Notify Slack'){
    sh 'curl -X POST --data-urlencode "payload={\\"channel\\": \\"#jenkins-deploy\\", \\"username\\": \\"jenkins\\", \\"text\\": \\"Ansible実行が完了しました。\\nBuild:${BUILD_URL}\\"}" ${WEBHOOK_URL}'
  }
}