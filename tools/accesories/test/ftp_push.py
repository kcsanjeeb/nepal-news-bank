import threading
from ftplib import FTP
import sys



def save_ftp( filename , my_ftp_url ,my_ftp_username ,my_ftp_password ,  my_ftp_remote_path , my_local_path):
    print(filename + my_ftp_url +my_ftp_username +my_ftp_password +  my_ftp_remote_path + my_local_path) 
    ftp = FTP(my_ftp_url ) 
    ftp.login(my_ftp_username,  my_ftp_password)   
    ftp.cwd(my_ftp_remote_path)
    file = open(my_local_path+filename,'rb')                  
    ftp.storbinary('STOR '+filename, file , 3000000)      
    file.close()
    ftp.quit()
    file_completed.append(filename)


     




if __name__ == "__main__":

    symss = '20210629_214154_24980_rough_cut.mp4 ftp.nepalnewsbank.com nepnews@nepalnewsbank.com nepnews test/ /files/'
   
   
    symss_array = symss.split(' ')

    my_ftp_url= 'ftp.nepalnewsbank.com'
    my_ftp_username= 'nepnews@nepalnewsbank.com'
    my_ftp_password = 'nepnews'

    my_ftp_path = 'test/'

    my_local_path = 'files/'


    file_csv = '20210629_214154_24980_rough_cut.mp4'
    file_array = file_csv.split(',')
    file_completed = []


    threads = []   
    rem_files = list(set(file_array) - set(file_completed) )


    # while(len(rem_files) > 0):
    for file_name in file_array:
        thread_task = threading.Thread(target=save_ftp, args=(file_name ,my_ftp_url , my_ftp_username ,my_ftp_password ,  my_ftp_path , my_local_path))
        thread_task.start()
        threads.append(thread_task)     
    

    for i in range(len(threads)):
        threads[i].join()
    
    rem_files = list(set(file_array) - set(file_completed) )

    

    completed_csv = ','.join(file_completed) 
    print(completed_csv)
  
  
       
     
