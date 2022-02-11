
import sys
print(sys.path)
import yagmail
from tkinter.tix import MAIN
import sqlalchemy
import db_classes
from sqlalchemy.orm import sessionmaker
import dbinfo

import os
import traceback
from time import sleep


def mail(course, week, account, password,subject,body,studentid,studentAddress):
    ret = True
    try:
        email=yagmail.SMTP(user=account,password=password,host="smtp.exmail.qq.com")
        contents=[body,r'.\RESULT\temp\%s\week%d\radarMap\%dWEEK%d.html'%(course,week, studentid, week)]
        email.send(studentAddress,subject,contents)
    except Exception as e:  # 如果 try 中的语句没有执行，则会执行下面的 ret=False
        print(traceback.format_exc())
        ret = False
    return ret

if __name__ == '__main__':
    try:  
        args = sys.argv 
        course = args[1]
        week = int(args[2])
        account=args[3]
        password=args[4]
        subject=args[5]
        body=args[6]
        SQLALCHEMY_DATABASE_URI = 'mysql+pymysql://' + dbinfo.user + ':' + dbinfo.password + '@' + dbinfo.host + '/' + course
        engine = sqlalchemy.create_engine(SQLALCHEMY_DATABASE_URI, echo=True)
        Session = sessionmaker(bind=engine)
        session = Session()
        students = session.query(db_classes.Persons.id).filter(db_classes.Persons.PersonRole == 1).all()

        for student in students:
            studentid = student[0]
            target = session.query(db_classes.Persons).filter(db_classes.Persons.id == studentid).first()
            studentAddress = target.Email
            print(studentid)
            print(studentAddress)
            ret = mail(course, week, account, password,subject,body,studentid,studentAddress)
            sleep(1)
            if ret:
                print("send email success")
            else:
                print("send email failed")
    except Exception as e:  
        print(traceback.format_exc())
