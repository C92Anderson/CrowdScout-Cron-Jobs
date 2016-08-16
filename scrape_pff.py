from lxml import html
from bs4 import BeautifulSoup as bs
import pandas as pd
import numpy as np
import html5lib
import lxml

teams = ['MIN', 'BAL']

url = "http://www.ourlads.com/nfldepthcharts/roster/MIN"

for team in teams:
    url = "http://www.ourlads.com/nfldepthcharts/roster/{}".format(team)
    team_dfs = pd.read_html(url)
    roster = team_dfs[0]
    print roster

URLs = ["http://www.ourlads.com/nfldepthcharts/roster/%s" % i for i in ['MIN']]

for url in URLs:
    page = requests.get(url).content
    soup = bs(page)
    print soup
###read in datasets
#for x in position:
#    str = '"C:\Users\colander1\Documents\CWA\PFFRatings.xlsx",sheetname="' + x + '"'
#    pd.read_excel(str)
#    pd.read_excel("C:\Users\colander1\Documents\CWA\PFFRatings.xlsx",sheetname=%s) % (x)

PFF_Passers = pd.read_excel("C:\Users\colander1\Documents\CWA\PFFRatings.xlsx",sheetname="Passers")
PFF_Runners = pd.read_excel("C:\Users\colander1\Documents\CWA\PFFRatings.xlsx",sheetname="Runners")
PFF_Receivers = pd.read_excel("C:\Users\colander1\Documents\CWA\PFFRatings.xlsx",sheetname="Receivers")
PFF_Blockers = pd.read_excel("C:\Users\colander1\Documents\CWA\PFFRatings.xlsx",sheetname="Blockers")
PFF_Secondary = pd.read_excel("C:\Users\colander1\Documents\CWA\PFFRatings.xlsx",sheetname="Secondary")
PFF_Backers = pd.read_excel("C:\Users\colander1\Documents\CWA\PFFRatings.xlsx",sheetname="Backers")
PFF_Linemen = pd.read_excel("C:\Users\colander1\Documents\CWA\PFFRatings.xlsx",sheetname="Linemen")

###fix names
PFF_Passers['NAME'] = PFF_Passers['NAME'].str.replace('.', '')
PFF_Runners['NAME'] = PFF_Runners['NAME'].str.replace('.', '')
PFF_Receivers['NAME'] = PFF_Receivers['NAME'].str.replace('.', '')
PFF_Blockers['NAME'] = PFF_Blockers['NAME'].str.replace('.', '')
PFF_Secondary['NAME'] = PFF_Secondary['NAME'].str.replace('.', '')
PFF_Backers['NAME'] = PFF_Backers['NAME'].str.replace('.', '')
PFF_Linemen['NAME'] = PFF_Linemen['NAME'].str.replace('.', '')

###fix names
#position = ['PFF_Passers','PFF_Runners','PFF_Receivers','PFF_Blockers','PFF_Secondary','PFF_Backers','PFF_Linemen']

#for pos in range(len(position)):
#for pos in position:
 #   print pos[2]
    #print pos.ix[2,0]
   #pos['NAME'] = pos['NAME'].str.replace('.', '')
#    pos['NAME'] = pos['NAME'].str.replace(r"\\(.+?\\)", '')
#    pos['NAME'] = pos['NAME'].str.replace("(Clifford) ", '')
#    pos['NAME'] = pos['NAME'].str.replace("(Clint) ", '')

###fix names manually
PFF_Backers['NAME'] = PFF_Backers['NAME'].str.replace(r"\\(.+?\\)", "")
PFF_Backers['NAME'] = PFF_Backers['NAME'].str.replace("(Clifford) ", "")
PFF_Backers['NAME'] = PFF_Backers['NAME'].str.replace('(Clint) ', '')

shortword = re.compile(r'\W*\b\w{1,2}\b')
PFF_Runners['NAME'] = str.replace(shortword, '')

    shortword.sub('', str(PFF_Runners['NAME']))

PFF_Runners['NAME'] = PFF_Runners['NAME'].str.replace('Jonathan C Stewart', 'Jonathan Stewart')


###export to mysql
conn = pg.connect(host='mysql.crowdscoutsports.com', user='ca_elo_games', password='cprice31!', port=3306, db='football_all')

PFF_Passers.to_sql(con=conn, name='PFF_Passers', if_exists='replace', flavor='mysql')
PFF_Runners.to_sql(con=conn, name='PFF_Runners', if_exists='replace', flavor='mysql')
PFF_Receivers.to_sql(con=conn, name='PFF_Receivers', if_exists='replace', flavor='mysql')
PFF_Blockers.to_sql(con=conn, name='PFF_Blockers', if_exists='replace', flavor='mysql')
PFF_Secondary.to_sql(con=conn, name='PFF_Secondary', if_exists='replace', flavor='mysql')
PFF_Backers.to_sql(con=conn, name='PFF_Backers', if_exists='replace', flavor='mysql')
PFF_Linemen.to_sql(con=conn, name='PFF_Linemen', if_exists='replace', flavor='mysql')





#position = ['PFF_Passers','PFF_Runners','PFF_Receivers','PFF_Blockers','PFF_Secondary','PFF_Backers','PFF_Linemen']

#for x in position:
#    import pymysql
#    x.to_sql(con=conn, name='x', if_exists='replace', flavor='mysql')