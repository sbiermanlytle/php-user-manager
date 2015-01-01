//
//  AbstractViewController.h
//  UserManager
//
//  Created by Sebastian Bierman-Lytle on 12/27/14.
//  Copyright (c) 2014 iio interactive. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "SSKeychain.h"
#import "LinkedTextField.h"

@interface BaseController : UIViewController

extern NSString* const URI;
extern NSString* const API;
extern NSString* const DL;

@property(strong) UIColor *color1;

@property(strong) UILabel *debug;
@property(strong) UIView *bottombar;
@property(strong) UIAlertView *alert;

@property(strong) UIView *loadingCircle;
@property(strong) UIActivityIndicatorView *loadingSpinner;
@property(strong) UILabel *loadingText;

@property(strong) NSData *httpdata;

@property CGFloat sw;
@property CGFloat sh;
@property CGFloat ny;
@property CGFloat nh;

-(void)logout;
-(void)back;

-(void)async_task:(SEL)background_task :(SEL)oncomplete_task :(BOOL)show_loading :(NSString*)loading_title;

-(void)alert:(NSString*)title message:(NSString*)text;

-(NSURL*)prepare_query:(NSString*)path params:(NSArray*)params;

-(LinkedTextField*) create_inputfield:(NSString*)title withFrame:(CGRect)frame;
-(UILabel*)create_uilabel:(NSString*)text withFrame:(CGRect)frame;
-(UIScrollView*)create_scrollView;

-(void)create_loadingSpinner:(CGFloat)diameter;
-(void)show_loadingSpinner:(NSString*)text;
-(void)hide_loadingSpinner;

-(void)create_bottombar;
-(UIButton*)create_button:(NSString*)title :(SEL)callback :(CGRect)frame;

@end
