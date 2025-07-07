# 🌐 Infraestrutura com Multipass, DNS e MicroCloud

## 🚀 Subindo as VMs com Multipass

```bash
multipass launch -n microcloud -c 2 -m 4G -d 20G
multipass launch -n dns -m 1G -d 5G
multipass launch -n devops -c 2 -m 2G -d 10G
```

# 📡 Configuração da VM dns

**Passos de configuração:**

```bash
sudo apt update
sudo apt install bind9
sudo nano /etc/bind/named.conf.options
ip -c a
sudo nano /etc/bind/named.conf.local
sudo nano /var/cache/bind/db.bookstorage.com
sudo systemctl restart bind9
sudo systemctl status bind9
```

**Arquivos de configuração:**
/etc/bind/named.conf.options

```bash
options {
    directory "/var/cache/bind";

    // Se houver um firewall entre você e os servidores de nomes que você deseja falar,
    // talvez seja necessário corrigir o firewall para permitir múltiplas portas para falar.
    // Veja http://www.kb.cert.org/vuls/id/800113

    // Se seu ISP forneceu um ou mais endereços IP para servidores de nomes estáveis,
    // provavelmente você deseja usá-los como encaminhadores.
    // Descomente o bloco a seguir e insira os endereços substituindo
    // o placeholder all-0's.

    // forwarders {
    //     0.0.0.0;
    // };

    //========================================================================
    // Se o BIND registrar mensagens de erro sobre a chave raiz estar expirada,
    // você precisará atualizar suas chaves. Veja https://www.isc.org/bind-keys
    //========================================================================
    dnssec-validation no;

    allow-query { 127.0.0.1; 10.116.4.0/24; };

    listen-on-v6 { any; };
};
```

/etc/bind/named.conf.local

```bash
//
// Do any local configuration here
//

// Consider adding the 1918 zones here, if they are not used in your
// organization
//include "/etc/bind/zones.rfc1918";

zone "bookstorage.com" IN {
        type master;
        file "db.bookstorage.com";
};
```

/var/cache/bind/db.bookstorage.com

```bash
$ORIGIN bookstorage.com.
$TTL 300
@ IN SOA dns jacson. (1 30 30 30 30)
@ IN NS dns
dns IN A 10.169.191.23
devops IN A 10.169.191.34
microcloud IN A 10.169.191.164
web IN A 10.169.191.200
db IN A 10.169.191.201
```

# ☁️ Configuração da VM microcloud

**Instalar e configurar o LXD**

```bash
sudo snap install lxd
lxd init --minimal
lxc config set core.https_address :8443  # dashboard
lxc config trust add --name terraform-access  # criar usuário. OBS: Guardar o código/certificado gerado!!!
```

# 🛠️ Configuração da VM devops

**Instalar o Terraform** [DOCS](https://developer.hashicorp.com/terraform/tutorials/aws-get-started/install-cli)

```bash
sudo snap install lxd
lxc remote add microcloud https://microcloud.bookstorage.com --auth-type=tls --token=colar_o_código
lxc remote list
```

**Criar e configurar o diretório do Terraform** [DOCS Provider](https://registry.terraform.io/providers/terraform-lxd/lxd/latest/docs)

```bash
mkdir terraform_microcloud
cd terraform_microcloud/
clear
nano main.tf
terraform init
```

# 🧾 Arquivo: main.tf

```hcl
terraform {
  required_providers {
    lxd = {
      source  = "terraform-lxd/lxd"
      version = ">= 2.5.0"
    }
  }
}

provider "lxd" {
  generate_client_certificates = true
  accept_remote_certificate    = true

  remote {
    name    = "microcloud"
    address = "https://microcloud.bookstorage.com:8443"
    token   = "colar_aqui_o_token_gerado_inicialmente"
  }
}

resource "lxd_network" "network_book" {
  name = "network_book"
  config = {
    "ipv4.address" = "10.150.19.1/24"
    "ipv4.nat"     = "true"
  }
}

resource "lxd_instance" "web" {
  name             = "web"
  image            = "ubuntu-daily:jammy"
  profiles         = ["default"]
  wait_for_network = false

  config = {
    "user.user-data" = <<-EOF
      #cloud-config
      ssh_authorized_keys:
        - "colar_a_chave_ssh"
      write_files:
        - path: /etc/netplan/01-netcfg.yaml
          content: |
            network:
              version: 2
              ethernets:
                eth0:
                  addresses: [10.150.19.100/24]
                  gateway4: 10.150.19.1
                  nameservers:
                    addresses: [10.150.19.1]
    EOF
  }

  device {
    name       = "eth0"
    type       = "nic"
    properties = {
      network        = lxd_network.network_book.name
      ipv4.address   = "10.150.19.100"
    }
  }
}

resource "lxd_instance" "db" {
  name             = "db"
  image            = "ubuntu-daily:jammy"
  profiles         = ["default"]
  wait_for_network = false

  config = {
    "user.user-data" = <<-EOF
      #cloud-config
      ssh_authorized_keys:
        - "colar_a_chave_ssh"
      write_files:
        - path: /etc/netplan/01-netcfg.yaml
          content: |
            network:
              version: 2
              ethernets:
                eth0:
                  addresses: [10.150.19.110/24]
                  gateway4: 10.150.19.1
                  nameservers:
                    addresses: [10.150.19.1]
    EOF
  }

  device {
    name       = "eth0"
    type       = "nic"
    properties = {
      network        = lxd_network.network_book.name
      ipv4.address   = "10.150.19.110"
    }
  }
}

resource "lxd_network_forward" "web_forward" {
  network        = lxd_network.network_book.name
  listen_address = "10.169.191.200"

  ports = [
    {
      description    = "SSH"
      protocol       = "tcp"
      listen_port    = 22
      target_port    = 22
      target_address = "10.150.19.100"
    },
    {
      description    = "HTTP"
      protocol       = "tcp"
      listen_port    = 80
      target_port    = 80
      target_address = "10.150.19.100"
    }
  ]
}

resource "lxd_network_forward" "db_forward" {
  network        = lxd_network.network_book.name
  listen_address = "10.169.191.201"

  ports = [
    {
      description    = "SSH"
      protocol       = "tcp"
      listen_port    = 22
      target_port    = 22
      target_address = "10.150.19.110"
    },
    {
      description    = "MySQL"
      protocol       = "tcp"
      listen_port    = 3306
      target_port    = 3306
      target_address = "10.150.19.110"
    }
  ]
}
```

# ✅ Aplicando com Terraform

```bash
terraform validate
terraform plan 
terraform apply
```
