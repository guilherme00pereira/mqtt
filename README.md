# MQTT

### OBSERVAÇÕES:


#### FUNCIONALIDADES DESCRITAS PARA O PLUGIN

1. **CONFIGURAÇÕES DO PLUGIN: CONEXÃO MQTT - CADASTRO DE SERVIDOR**

	Cada usuário pode cadastrar suas informações de um servidor MQTT
	O plugin irá se conectar ao servidor mqtt e gravar no banco de dados as informações publicadas nos tópicos (USAREMOS COM EXEMPLO O PLUGIN MQTTCOGS) atribuídas ao usuário específico e ao admin que deverá ter acesso a todas. OBS Já tenho um plugin funcional de exemplo que se conecta o MQTT e grava os dados no banco de dados. Já se pode iniciar o projeto com dados deste plugin como exemplo.


>OK  = Ajuste: Na lista de servidores, mostrar o estatus da última conexão, se bem o mal sucedida. **DONE**


2. **CADASTRO AUTOMÁTICO DE DISPOSITIVOS**

	2.1. O nosso plugin vai ler no banco de dados os registros do MTQQ para extrair dele os dados json que precisa

  2.2. Cada vez que o plugin encontrar um novo registro deverá extrir dele o mac do dispositivo. Exemplo:  No json acima o maAdress a ser extraído será 34:D0:X8:70:E2:6B. E então este 34D0X870E26B será o dispositivo cadastrado e atribuído ao usuário "dono" do servidor cadastrado.
	
	SEQUÊNCIA DE TAREFAS

		A. LER O JSON NO BANCO DE DADOS E EXTRAIR A INFORMAÇÃO DO MAC
		B. COMPARAR SE ESTE MAC JÁ EXISTE, OU SEJA SE ESTE DISPOSITIVO JÁ ESTÁ CADASTRADO
		C. SE O DISPOSITIVO NÃO ESTIVER CADASTRADO, CADASTRÁ-LO USANDO O MAC EXTRAÍDO, ATRIBUINDO-O AO USUÁRIO QUE CADASTROU O SERVIDOR CORRESPONDENTE


>OK = 2.1 OK, 
>2.2 = Pequenos ajustes: Na lista dispositivos, na coluna autor, mostrar o usuário que cadastrou o servidor como dono do dispositivo(Ver item 2.2, sub item c) >Internamente o campo onde mostra a qual servidor ele pertence deve ser verificado e atualizado se houver mudança a cada execução do cronjobs **DONE**


3. **FORMULÁRIO DE EDIÇÃO MANUAL DO CADASTRO AUTOMÁTICO DOS DISPOSITIVOS**
	
  Cada dispositivo cadastrado automaticamente deverá ter a possibilidade de acrescentarmos informações extras que devarão ser preenchida via um formulário simples com informações como: <br />
		A. NOME DO CLIENTE:<br />
		B. ENDEREÇO DO DISPOSITIVO:<br />
		C. RESPONSÁVEL:<br />
		D. CONTATO:<br />
		E. OBSERVAÇÕES:<br />

>OK **DONE**

4. **EXTRAÇÃO DE DADOS DO JSON E GERAÇÃO DE ESTATÍSTICAS** 

4.1. Em cada dispositivo cadastrado poderemos ver as suas informações extraídas do json de exemplo acima (2.1), que serão:

		A-)NOME DO DISPOSITIVO ("deviceName)
		B-)*PASTA SINCRONIZADA* (link para acessarmos uma determinada pasta através de um gerenciador de arquivos como elFinder)
		C-)MAC DO DISPOSITIVO (macAddress)
		D-)ÚLTIMA COMUNICAÇÃO COM O SERVIDOR (hora do último registro)
		E-)TEMPO LIGADO (uptime)
		F-)ÚLTIMA MÍDIA EXIBIDA (lastDisplayedFile)
		G-)PLAYLIST ATUAL (currentPlaylist)
		H-)LAYOUT ATUAL (currentScreenLayout)
		I-)VOLUME ATUAL (currentVolume)
		J-)IP DA REDE LOCAL (ipAddressInternal)
		K-)ESPAÇO LIVRE EM DISCO (2992267264)
		L-)FUSO HORÁRIO (timeZone)

>Pendente  
>Estas informações devem ser mostrados "dentro" de cada dispositivo (ao clicarmos em editar) ou em uma página específica para cada um ao clicarmos em "ver"
>Estas informações devem estar sempre atualizadas a cada execução do cronjobs **DONE**

5. **GERAÇÃO DE ESTATÍSTICAS:**

O PLUGIN DEVERÁ TER UMA PÁGINA DE ESTATÍSTICAS DE MÍDIAS EXIBIDAS, GERAL E POR DISPOSITIVO, INFORMANDO O NÚMERO DE VEZES E O TEMPO TOTAL DE EXIBIÇÃO DE CADA MÍDIA

>Pendente
>As estatísticas deverão ser mostradas para cada dispositivo e na mesma página e de mesma maneira que as informações acima, ou seja, ou "dentro" de cada dispositivo (ao clicarmos em editar) ou em uma página específica para cada um ao clicarmos em "ver".
>As estatísticas devem ser organizadas pela mídia exibida (nome do arquivo), mostrando o número de vezes e tempo de total de cada mídia, a data do primeiro e do último registro.
>A página de estatísticas criada page=mqtt-connection pode ser uma estatística geral, somando de todos os aparelhos, mas organizando pelo nome do arquivo da mídia.
>Estas informações devem estar sempre atualizadas a cada execução do cronjobs e talvez seja interessante criar novas tabelas para organizar as estatatísticas

>Obs: Sugestão: as tabelas dos resgitros de dados mqtt acho que podem ser apagadas após processamento e respectiva "organização" dos dados cadastrando dispositivos e/ou adicionando estatísticas


6. **BOTÕES DE COMANDO**

Em cada dispositivo deveremos ter também botões ou campos para enviar via MQTT comandos em json que executarão as seguintes funções:

		A-)Ir para o próximo arquivo - Exemplo: {"operation": "next"}	
		B-)Alternar visualização em tela cheia - Exemplo: {"operation": "fullscreen/toggle"}	
		C-)Definir volume - Exemplo: {"operation": "volume/set", "parameters": {"vol": 5}}
		D-)Recarregar aplicativo - Exemplo: {"operation": "reload"}
		E-)Reinicializar dispositivo - Exemplo: {"operation": "reboot"}		


>Pendente
	Os botões estão sendo mostrados na página dos servidores, quando na verdade deveriam ser em cada dispositivo e fazerem "post" no servidor mqtt ao qual o dispositivo está conectado, mas ao enviar os dados (OBS: Podemos trabalhar isto com calma em um UPGRADE À parte)


7. **FERRAMENTA DE GERAR NOME DE ARQUIVO**

O plugin deverá ter uma ferramenta simples de geração do nome do arquivo de exclusão agendada, bastando digitar o nome e extensão do arquivo e a data e hora de exclusão, que ele irá gerar o nome especial com o qual o usuário deverá renomear o arquivo, exemplos: 

		imagem_DEL_2020-11-22.jpg
		video_DEL_2020-11-22 21:00.mp4
		modelo-banner_DEL_22.11.2020.jpg
		documento_DEL_7D.pdf

>OK = Ajuste simples em futuro UPGRADE a combinarmos, como datepicker para seção da data e javascrit para copiar automaticamente o resultado para área de transferência (ctr+c)