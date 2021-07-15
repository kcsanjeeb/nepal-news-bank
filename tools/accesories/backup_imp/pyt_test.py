from itertools import islice
import threading
import datetime;


def chunk(it, size):
    it = iter(it)
    return iter(lambda: tuple(islice(it, size)), ())

def save_ftp(file):
    ct = datetime.datetime.now()
    print("current time:-", ct) 
    file_completed.append(file)
    print(file)


if __name__ == "__main__":

    file_list = ['यसरी गए ओली बालुवाटारबाट बालकोट ','file2','file3','file4','file5','file6','file7','file8','file9']
    file_name_csv = list(chunk(file_list, 6))


    file_completed = []
    threads = [] 


    
    for file_name_list in file_name_csv:
        for file in file_name_list:
            thread_task = threading.Thread(target=save_ftp(file))
            thread_task.start()
            threads.append(thread_task) 

            for i in range(len(threads)):
                threads[i].join()
                
        
