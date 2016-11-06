# -*- coding: utf-8 -*- 
#sicongxie@tencent.com

import os
import sys
#import gensim
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.cross_validation import train_test_split
from sklearn.naive_bayes import MultinomialNB
from sklearn.naive_bayes import GaussianNB
from sklearn.naive_bayes import BernoulliNB  
from sklearn.linear_model import LogisticRegression
from sklearn.ensemble import RandomForestClassifier
from sklearn import svm
from sklearn import tree
from sklearn import cross_validation
from sklearn import metrics 
from sklearn import grid_search
from sklearn.pipeline import Pipeline 
from sklearn.externals import joblib
from random import shuffle
from optparse import OptionParser
import random
try:
    import cPickle as pickle
except ImportError:
    import pickle
import numpy as np
from pandas import DataFrame
import matplotlib.pyplot as plt
import seaborn as sns
from sklearn.model_selection import learning_curve
from sklearn.model_selection import ShuffleSplit

reload(sys)
sys.setdefaultencoding('utf8')

op = OptionParser()
op.add_option("-f","--file",action="store",type="string",dest="filename",help="odds file")
op.add_option("-p","--pg",action="store_true", dest="pipe_grid",help="use pipeline + grid search")
op.add_option("-u","--update",action="store_true", dest="update",help="update the model")
(opts, args) = op.parse_args()

comp_array = ["威廉希尔","澳门","立博","Bet365","Interwetten","SNAI","伟德","Bwin","Coral","SportingBet(博天堂)"]

def plot_learning_curve(estimator, title, X, y, ylim=None, cv=None,
                        n_jobs=1, train_sizes=np.linspace(.1, 1.0, 5)):
    """
    Generate a simple plot of the test and training learning curve.

    Parameters
    ----------
    estimator : object type that implements the "fit" and "predict" methods
        An object of that type which is cloned for each validation.

    title : string
        Title for the chart.

    X : array-like, shape (n_samples, n_features)
        Training vector, where n_samples is the number of samples and
        n_features is the number of features.

    y : array-like, shape (n_samples) or (n_samples, n_features), optional
        Target relative to X for classification or regression;
        None for unsupervised learning.

    ylim : tuple, shape (ymin, ymax), optional
        Defines minimum and maximum yvalues plotted.

    cv : int, cross-validation generator or an iterable, optional
        Determines the cross-validation splitting strategy.
        Possible inputs for cv are:
          - None, to use the default 3-fold cross-validation,
          - integer, to specify the number of folds.
          - An object to be used as a cross-validation generator.
          - An iterable yielding train/test splits.

        For integer/None inputs, if ``y`` is binary or multiclass,
        :class:`StratifiedKFold` used. If the estimator is not a classifier
        or if ``y`` is neither binary nor multiclass, :class:`KFold` is used.

        Refer :ref:`User Guide <cross_validation>` for the various
        cross-validators that can be used here.

    n_jobs : integer, optional
        Number of jobs to run in parallel (default 1).
    """
    plt.figure()
    plt.title(title)
    if ylim is not None:
        plt.ylim(*ylim)
    plt.xlabel("Training examples")
    plt.ylabel("Score")
    train_sizes, train_scores, test_scores = learning_curve(
        estimator, X, y, cv=cv, n_jobs=n_jobs, train_sizes=train_sizes)
    train_scores_mean = np.mean(train_scores, axis=1)
    train_scores_std = np.std(train_scores, axis=1)
    test_scores_mean = np.mean(test_scores, axis=1)
    test_scores_std = np.std(test_scores, axis=1)
    plt.grid()

    plt.fill_between(train_sizes, train_scores_mean - train_scores_std,
                     train_scores_mean + train_scores_std, alpha=0.1,
                     color="r")
    plt.fill_between(train_sizes, test_scores_mean - test_scores_std,
                     test_scores_mean + test_scores_std, alpha=0.1, color="g")
    plt.plot(train_sizes, train_scores_mean, 'o-', color="r",
             label="Training score")
    plt.plot(train_sizes, test_scores_mean, 'o-', color="g",
             label="Cross-validation score")

    plt.legend(loc="best")
    return plt

def gen_corpus(filename):
    data = {}
    file = open(filename)
    courpus = []
    target = []
    while 1:
        lines = file.readlines(100000)
        if not lines:
            break
        for line in lines:  
            i = 1
            count = 0
            str = line.replace("\n",'')     
            items = str.split(' ')
            num = items[0]
            data[num] = {}
            courpus_line = []
            bad = 0
            #courpus_line.append(num)
            for comp in comp_array:
                if items[i] == '0':
                    #print num,comp
                    bad = 1
                    break;
                '''
                if comp == 'Coral':
                    i = i + 6;
                    continue
                '''
                data[num][comp] = {}
                data[num][comp]['first'] = {}
                data[num][comp]['end'] = {}
                data[num][comp]['first']['win'] = float(items[i]);
                data[num][comp]['first']['draw'] = float(items[i+1]);
                data[num][comp]['first']['lost'] = float(items[i+2]);
                data[num][comp]['end']['win'] = float(items[i+3]);
                data[num][comp]['end']['draw'] = float(items[i+4]);
                data[num][comp]['end']['lost'] = float(items[i+5]);
                courpus_line.append(float(items[i]))
                courpus_line.append(float(items[i+1]))
                courpus_line.append(float(items[i+2]))
                courpus_line.append(float(items[i+3]))
                courpus_line.append(float(items[i+4]))
                courpus_line.append(float(items[i+5]))
                i = i + 6;
                #print comp,data[comp]
                count = count + 1
            if bad == 1:
                continue
            data[num]['res'] = int(items[i])
            data[num]['home'] = float(items[i+1])
            data[num]['away'] = float(items[i+2])
            data[num]['time'] = float(items[i+3])
            #courpus_line.append(int(items[i+1]))
            #courpus_line.append(int(items[i+2]))
            target.append(int(items[i]))
            courpus.append(courpus_line)
    #print courpus[231],target[231]
    return courpus,target

def process(corpus, target):
    '''
    from seaborn.linearmodels import corrplot
    y = np.array(target)
    x = np.array(corpus)
    print x.shape
    df = DataFrame(np.hstack((x, y[:,None])),columns = range(x.shape[1]) + ["class"]) 
    #_ = sns.pairplot(df[:100], vars=[0,1,2,3,4,5], hue="class", size=1.5)
    plt.figure(figsize=(20, 20))
    _ = corrplot(df, annot=False)
    sns.plt.show()
	'''
    
    rint = random.randint(0,99);
    print "train_test_split randint %d" %(rint)
    raw_other, raw_test, y_other, y_test = train_test_split(corpus, target, train_size=0.8, random_state=rint)
    print raw_other[31]
    print y_other[31]

    X_train = np.array(raw_other[len(raw_other)/2+1:])
    y_train = np.array(y_other[len(y_other)/2+1:])
    X_test = np.array(raw_test)
    y_test = np.array(y_test)
    X_cv = np.array(raw_other[0:len(raw_other)/2])
    y_cv = np.array(y_other[0:len(y_other)/2])
    '''
    #training data
    tv1 = TfidfVectorizer(vocabulary = tv.vocabulary_)
    #X_train = tv1.fit_transform(raw_other[len(raw_other)/2+1:])
    #y_train = y_other[len(y_other)/2+1:]
    X_train = tv1.fit_transform(raw_other)
    y_train = y_other
    
    #test data
    tv2 = TfidfVectorizer(vocabulary = tv.vocabulary_)
    X_test = tv2.fit_transform(raw_test)
    
    #cv data
    X_cv_raw = raw_other[0:len(raw_other)/2]
    tv3 = TfidfVectorizer(vocabulary = tv.vocabulary_)
    X_cv = tv3.fit_transform(X_cv_raw)
    y_cv = y_other[0:len(y_other)/2]
    '''
    print X_train.shape
    print X_cv.shape
    print X_test.shape
    
    title = "Learning Curves (SVM, RBF kernel, $\gamma=0.001$)"
    cv = ShuffleSplit(n_splits=10, test_size=0.2, random_state=0)
    estimator = svm.SVC(gamma=0.001)
    img = plot_learning_curve(estimator, title, X_train, y_train, cv=cv, n_jobs=4)
    img.show()
    estimator = MultinomialNB()
    img = plot_learning_curve(estimator, title, X_train, y_train, cv=cv, n_jobs=4)
    img.show()

    score_type = 'precision'
    print "CV score:",score_type
    
    parameters = {'alpha':[0.0001, 0.001, 0.01, 0.1, 1],'fit_prior':[True,False]}
    clf = grid_search.GridSearchCV(MultinomialNB(), parameters,cv=10)
    clf.fit(X_train, y_train)
    print clf.best_params_ 
    print clf.grid_scores_  
    predict = clf.predict(X_test)   
    calculate_result('MultinomialNB',y_test,predict)
    print "CV result:"
    score =cross_validation.cross_val_score(clf, X_cv, y_cv, cv=10, scoring=score_type)
    print score
    print("Accuracy: %0.2f (+/- %0.2f)" % (score.mean(), score.std() * 2))
    
    
    clf = LogisticRegression()
    clf.fit(X_train,y_train)
    predict = clf.predict(X_test)   
    calculate_result('LR',y_test,predict)
    print "CV result:"
    score =cross_validation.cross_val_score(clf, X_cv, y_cv, cv=10, scoring=score_type)
    print score
    print("Accuracy: %0.2f (+/- %0.2f)" % (score.mean(), score.std() * 2))
    '''
    if opts.update == True:
        joblib.dump(clf, './model/org_model.pkl') 
        f = open('./model/voc.txt', 'wb')
        pickle.dump(tv.vocabulary_, f)
        print len(tv.vocabulary_)
        f.close()
        sys.exit()
    '''
    #clf = svm.SVC(kernel = 'linear')
    parameters = {'C':[2**(-5),2**(-4),2**(-3),2**(-2),2**(-1),1,2**2],'kernel':['rbf','linear']}
    clf = grid_search.GridSearchCV(svm.SVC(), parameters,cv=10)
    clf.fit(X_train,y_train)
    print clf.best_params_ 
    print clf.grid_scores_  
    predict = clf.predict(X_test)   
    calculate_result('svm',y_test,predict)
    print "CV result:"
    score =cross_validation.cross_val_score(clf, X_cv, y_cv, cv=10, scoring=score_type)
    print score
    print("Accuracy: %0.2f (+/- %0.2f)" % (score.mean(), score.std() * 2))
    
    clf.fit(X_train,y_train)
    if opts.update == True:
        joblib.dump(clf, './model/org_model.pkl') 
        f = open('./model/voc.txt', 'wb')
        pickle.dump(tv.vocabulary_, f)
        print len(tv.vocabulary_)
        f.close()
    
def calculate_result(name,actual,pred):
    print '============================'
    print name  
    print 'predict info of "1":'  
    m_precision = metrics.precision_score(actual,pred,pos_label=1);  
    m_recall = metrics.recall_score(actual,pred,pos_label=1);  
    print 'precision:{0:.3f}'.format(m_precision)  
    print 'recall:{0:0.3f}'.format(m_recall);  
    print 'f1-score:{0:.3f}'.format(metrics.f1_score(actual,pred,pos_label=1));      
 
    print 'predict info of "0":'  
    m_precision = metrics.precision_score(actual,pred,pos_label=0);  
    m_recall = metrics.recall_score(actual,pred,pos_label=0);  
    print 'precision:{0:.3f}'.format(m_precision)  
    print 'recall:{0:0.3f}'.format(m_recall);  
    print 'f1-score:{0:.3f}'.format(metrics.f1_score(actual,pred,pos_label=0));      

if __name__ == "__main__":
    print opts.filename 
    courpus,target = gen_corpus(opts.filename)
    process(courpus, target)

