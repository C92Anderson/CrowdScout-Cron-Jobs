import sys
print sys.path

import time
import pandas as pd
import pymysql as pg
import numpy as np
#import plotly.plotly as py
#from plotly.graph_objs import *

conn = pg.connect(host='mysql.crowdscoutsports.com', user='ca_elo_games', password='cprice31!')
cur = conn.cursor()

###pull all
cur.execute("SELECT tm_nm, team, nhl_id,(CURRENT_DATE - dob) /365.25 as age, pos, pnm as player, Score FROM `nhl_all`.hockey_daily_elo_py as A inner join `nhl_all`.hockey_roster_R as B on A.ID = B.nhl_id where cast(A.Date as Date) = CURRENT_DATE()")

results = cur.fetchall()
results_list = [list(row) for row in results]

###create dataframe
rosterToday= pd.DataFrame(results_list, columns=['Team','TeamAbb','ID','Age','Pos','Player','Score'])


###select unique list of team
teamList = df['TeamAbb'].unique()

###player elo day
eloDay = pd.DataFrame()

for i in teamList:
    player1 = pd.DataFrame(df.loc[df['Player'] == i])
    player1 = dates.join(player1, how='left')
    player1 = player1.fillna(method='ffill')
    player1 = player1.fillna(method='bfill')
    eloDay = pd.concat([eloDay,player1])

###calculate max elo date by day
eloDay['Elo'] = eloDay[['Elo']].astype(float)
eloDayRange = pd.DataFrame(eloDay.groupby(level=0)['Elo'].agg({'EloMax' : np.max,'EloMin' : np.min}))

###merge on daily max to player dataset
eloDayRange = eloDay.merge(eloDayRange, how='left', left_index=True, right_index=True)

###calculate score
eloDayRange['Score'] = ((eloDayRange.Elo - eloDayRange.EloMin) * 100)/ (eloDayRange.EloMax - eloDayRange.EloMin)
eloDayRange['Date'] = eloDayRange.index
eloDayRange = eloDayRange.reset_index(drop=True)
eloDayRange['cron_ts'] = pd.to_datetime(time.strftime("%Y-%m-%d %X"))

###insert into phpmyadmin
conn2 = pg.connect(host='mysql.crowdscoutsports.com', user='ca_elo_games', password='cprice31!',
                   port=3306, db='nhl_all')
eloDayRange.to_sql(con=conn2, name='', if_exists='replace', flavor='mysql')

conn.commit()
conn.close()