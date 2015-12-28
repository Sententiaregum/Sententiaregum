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

import React from 'react';
import TestUtils from 'react/lib/ReactTestUtils';
import NotFoundPage from '../../../components/app/NotFoundPage';
import chai from 'chai';

describe('NotFoundPage', () => {
  it('renders a 404 page', () => {
    const renderer = TestUtils.createRenderer();
    renderer.render(<NotFoundPage />);

    const component = renderer.getRenderOutput();
    const translate = component.props.children[1].props.children;
    const heading   = translate[0].props.children.props.content;
    const body      = translate[1].props.children.props.content;

    chai.expect(heading).to.equal('pages.not_found.title');
    chai.expect(body).to.equal('pages.not_found.text');
  });
});
