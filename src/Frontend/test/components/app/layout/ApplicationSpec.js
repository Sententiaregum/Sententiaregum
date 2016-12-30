/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian.bosch.27@gmail.com>
 * (c) Ben Bieler <benjaminbieler2014@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

'use strict';

import React       from 'react';
import { expect }  from 'chai';
import { shallow } from 'enzyme';
import Application from '../../../../components/app/layout/Application';

describe('Application', () => {
  it('renders the container for the whole application', () => {
    const wrapper = shallow(<Application><h1>Hello World!</h1></Application>);

    expect(wrapper.find('div').hasClass('container')).to.equal(true);
    expect(wrapper.find('div > h1').contains('Hello World!')).to.equal(true);
  });
});
