import threading
from ftplib import FTP
import sys



def save_ftp( filename , my_ftp_url ,my_ftp_username ,my_ftp_password ,  my_ftp_remote_path , my_local_path):

    ftp = FTP(my_ftp_url ) 
    ftp.login(my_ftp_username,  my_ftp_password)   
    ftp.cwd(my_ftp_remote_path)
    file = open(my_local_path+filename,'rb')                  
    ftp.storbinary('STOR '+filename, file , 3000000)      
    file.close()
    ftp.quit()
    file_completed.append(filename)


     




if __name__ == "__main__":


    my_ftp_url= sys.argv[2]
    my_ftp_username= sys.argv[3]
    my_ftp_password = sys.argv[4]

    my_ftp_path = sys.argv[5] 
    my_ftp_path = my_ftp_path.replace("+-*", " ")
    my_local_path = sys.argv[6] 
    my_local_path = my_local_path.replace("+-*", " ")


    file_csv = sys.argv[1]
    file_array = file_csv.split(',')
    file_completed = []


    threads = []   
    rem_files = list(set(file_array) - set(file_completed) )


    while(len(rem_files) > 0):
        for file_name in file_array:
            thread_task = threading.Thread(target=save_ftp, args=(file_name ,my_ftp_url , my_ftp_username ,my_ftp_password ,  my_ftp_path , my_local_path))
            thread_task.start()
            threads.append(thread_task)     
       
   
        for i in range(len(threads)):
            threads[i].join()
        
        rem_files = list(set(file_array) - set(file_completed) )

    

    completed_csv = ','.join(file_completed) 
    print(completed_csv)
  
  
       
     
