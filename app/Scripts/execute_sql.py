import mysql.connector
import sys


def execute_sql(sql_command):
    try:
        connection = mysql.connector.connect(
            host='localhost',
            user='root',
            password='',
            database='AutoBasisData'
        )
        cursor = connection.cursor()
        cursor.execute(sql_command)
        connection.commit()
        return cursor.fetchall()
    except mysql.connector.Error as err:
        print(f'Error: {err}')
        return None
    finally:
        if connection.is_connected():
            cursor.close()
            connection.close()


if __name__ == '__main__':
    sql = sys.argv[1]
    result = execute_sql(sql)
    print(result)
