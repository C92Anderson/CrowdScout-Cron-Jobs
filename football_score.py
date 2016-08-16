import sys
print sys.path

import time
import pandas as pd
import pymysql as pg
import numpy as np

conn = pg.connect(host='mysql.crowdscoutsports.com', user='ca_elo_games', password='cprice31!')
cur = conn.cursor()

cur.execute("SELECT A.cs_id, coalesce(B.player_name,A.player_name) as player_name, Date, elo FROM (SELECT cs_id, CAST( game_ts AS DATE ) as Date, player_name, elo FROM `football_all`.`football_elo_v1` as A WHERE cs_id != '' group by cs_id, Date, player_name) as A INNER JOIN `football_all`.football_roster_R as B ON A.cs_id=B.cs_id WHERE A.player_name != ''")

results = cur.fetchall()
results_list = [list(row) for row in results]

###create dataframe
df = pd.DataFrame(results_list, columns=['ID','Player','Date','Elo'])

###set to date and set index
df["Date"] = pd.to_datetime(df["Date"])
df = df.set_index(['Date'])

####create dates - date index
dates = pd.DataFrame(index=pd.date_range('20151001',time.strftime("%Y%m%d"), freq='D'))

###select unique list of players
playerList = df['ID'].unique()

###player elo day
eloDay = pd.DataFrame()

for i in playerList:
    player1 = pd.DataFrame(df.loc[df['ID'] == i])
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
eloDayRange = eloDayRange.set_index(['ID'])

###insert into phpmyadmin
conn2 = pg.connect(host='mysql.crowdscoutsports.com', user='ca_elo_games', password='cprice31!',
                   port=3306, db='football_all')
eloDayRange.to_sql(con=conn2, name='football_daily_elo_py', if_exists='replace', flavor='mysql')

conn.commit()
conn.close()