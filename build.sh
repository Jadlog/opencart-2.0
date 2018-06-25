#!/usr/bin/env bash
rm ./package/jadlog.ocmod.zip
cd src/jadlog.ocmod/
zip -r ../../package/jadlog.ocmod.zip *
cd ../..
ls -lah ./package/jadlog.ocmod.zip
unzip -l ./package/jadlog.ocmod.zip

#atualizar plugin
if [ "$1" = "docker" ]; then
  echo "Copiando arquivos para o docker..."
  if [ -z "$2" ]; then
    export dockeropencart=docker-opencart_web_1
  else
    export dockeropencart="$2"
  fi
  docker cp ./src/jadlog.ocmod/. $dockeropencart:/var/www/html/
  docker exec -it $dockeropencart sh -c "chown -R www-data.www-data /var/www/html"
  echo "Ok."
fi
echo "build.sh - uso: "
echo "    Para apenas gerar o pacote jadlog.ocmod.zip: "
echo "      ./build.sh"
echo "    Para gerar o pacote jadlog.ocmod.zip e copiar o código para o container docker: "
echo "      ./build.sh docker [<nome do container>]"
echo -e "      \e[3mCaso não preencha o <nome do container> utiliza o nome padrão \e[1mdocker-opencart_web_1\e[0m"
